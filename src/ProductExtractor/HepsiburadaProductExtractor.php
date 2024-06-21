<?php namespace App\ProductExtractor;

use App\Entity\Marketplace;
use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.hepsiburada.com')]
class HepsiburadaProductExtractor
{

    const VARIANT_IMAGE_SIZE = 600;
    const EXTRACT_ALL_VARIANTS = FALSE;

    public function __construct(private WebScrapingRequestExtractorHelper $extractorHelper)
    {
    }

    public function __invoke(WebScrapingRequestExtractProductsEvent $myEvent): void
    {
        // Get Crawler & XHR Logs
        $myCrawler = $this->extractorHelper->getCrawler($myEvent->getWebScrapingRequest(), $myEvent->getMarketplace());
        $myXHRLog = $this->extractorHelper->getXHRLog($myEvent->getWebScrapingRequest(), $myEvent->getMarketplace());

        // Extract Products With DOM Crawler
        if ($myCrawler instanceof Crawler) {

            // Create Array Collection For Extracted Products
            $extractedProducts = new ArrayCollection();

            // Extract Moria Inner Text
            $moriaProductList = $myCrawler->filterXPath('//div[contains(@class,"product-list-area")]//div[contains(@class,"voltran-fragment")]//div//script[@type][contains(.,"window.MORIA.PRODUCTLIST")]');
            if ($moriaProductList->count() > 0) {
                $moriaOuterHTML = $moriaProductList->html();

                // Extract Data With Outer HTML
                $moriaProductsArr = $this->extractProductsByMoria($moriaOuterHTML);
                if (is_array($moriaProductsArr)) {
                    $extractedProducts = $this->extractProductWithArrayData($moriaProductsArr, $myEvent->getMarketplace());
                }
            }

            // Flush Extracted Products
            $this->extractorHelper->pushProducts($extractedProducts, $myEvent->getMarketplace());

            // Flush Counts
            $this->extractorHelper->pushCounts($myEvent->getWebScrapingRequest(), $extractedProducts->count(), $extractedProducts->count());

        }

        // Extract Products With XHR Logs
        if (is_array($myXHRLog)) {
            // TODO : Extract Product With XHR
        }

    }

    private function extractProductsByMoria(string $moriaProductsJS): null|array
    {
        $scriptWithNoJsTag = str_replace(['</script>', '<script type="text/javascript">'], ['', ''], $moriaProductsJS);
        $scriptWithoutEOL = str_replace(PHP_EOL, '', $scriptWithNoJsTag);
        $scriptWithoutEOLTrimmed = trim($scriptWithoutEOL);

        $stateExplodedData = explode('\'STATE\': ', $scriptWithoutEOLTrimmed);

        if (count($stateExplodedData) === 2) {
            $innerJSONData = $stateExplodedData[1];
            if (str_ends_with($innerJSONData, ')')) {
                $innerJSONData = mb_substr($innerJSONData, 0, strlen($innerJSONData) - 1);
            }
            $finalJSON = $this->trimLastParenthesis($innerJSONData, 2);
            if (json_validate($finalJSON) === TRUE) {
                $decodedJSON = json_decode($finalJSON, true, 512, JSON_OBJECT_AS_ARRAY);
                return (is_array($decodedJSON) || is_object($decodedJSON)) ? (array)$decodedJSON : NULL;
            }

        }
        return NULL;
    }


    private function trimLastParenthesis(string $rawString, int $trimLast = 1): string
    {
        for ($x = 0; $x < $trimLast; $x++) {
            $rawString = rtrim($rawString);
            $rawString = trim(rtrim($rawString, '}'));
        }
        return $rawString;
    }

    private function extractProductWithArrayData(array $rawData, Marketplace $marketplace): ArrayCollection
    {
        $extractedProducts = new ArrayCollection();

        if (isset($rawData["data"])) {
            $theData = $rawData["data"];
            if (isset($theData["products"])) {
                $allProducts = $theData["products"];
                if (is_array($allProducts)) {
                    foreach ($allProducts as $moriaProductData) {

                        $productID = $moriaProductData["productId"];


                        if (isset($moriaProductData["variantList"])) {
                            // DEPRECED : This is main product identity $productIdentity = $moriaProductData["productId"];
                            $productVariantList = $moriaProductData["variantList"];

                            foreach ($productVariantList as $productVariant) {

                                if (isset($productVariant["sku"]) && isset($productVariant["name"]) && isset($productVariant["images"]) && isset($productVariant["url"])) {

                                    $variantImageURLPlaceholder = $this->findVariantDefaultImage($productVariant["images"]);
                                    $variantIdentity = $productVariant["sku"];
                                    $variantName = $productVariant["name"];
                                    $variantURL = $this->fixVariantURL($productVariant["url"], $marketplace);
                                    $variantImage = $variantImageURLPlaceholder !== NULL ? str_replace('{size}', self::VARIANT_IMAGE_SIZE, $variantImageURLPlaceholder) : NULL;

                                    // Check Var
                                    $variantIsExtractable = self::EXTRACT_ALL_VARIANTS === TRUE || $productVariant["isDefault"] === TRUE;

                                    // Check Variables
                                    if (strlen($variantIdentity) > 0 && strlen($variantName) > 0 && $variantURL !== NULL && $variantImage !== NULL && $variantIsExtractable === TRUE) {

                                        // User Crawler And Create Product
                                        $myProduct = new Product();
                                        $myProduct->setIdentity($variantIdentity);
                                        $myProduct->setName($variantName);
                                        $myProduct->setImage($variantImage);
                                        $myProduct->setUrl($variantURL);

                                        // Add Products
                                        $extractedProducts->add($myProduct);

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $extractedProducts;
    }

    private function findVariantDefaultImage(array $variantImagesData): null|string
    {
        // Select Default Image
        foreach ($variantImagesData as $variantImagesDatum) {
            if ($variantImagesDatum["isDefault"] === TRUE) {
                return $variantImagesDatum["link"];
            }
        }
        // Or Select Random Image
        if (count($variantImagesData) > 0) {
            return $variantImagesData[array_key_first($variantImagesData)];
        }

        // Or Return NULL
        return NULL;
    }

    private function fixVariantURL(?string $variantURL, MArketplace $marketplace): string|null
    {
        if (is_string($variantURL)) {
            $parsedVariantURL = parse_url($variantURL, PHP_URL_HOST);
            if (!is_string($parsedVariantURL)) {
                return $marketplace->getUrl() . (str_starts_with($variantURL, '/') ? $variantURL : '/' . $variantURL);
            }
        }

        return NULL;
    }
}