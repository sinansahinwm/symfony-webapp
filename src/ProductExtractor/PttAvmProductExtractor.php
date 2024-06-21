<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.pttavm.com')]
class PttAvmProductExtractor
{

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

            // Focus Products
            $filteredProducts = $myCrawler->filterXPath('//div[@data-v-3d8f1a36][@data-v-30d521fa][contains(@class,"product-list-box")]');
            $filteredProducts->each(function (Crawler $crawler) use ($myEvent, $extractedProducts) {

                // Get Product Data
                $productURL = $crawler->filterXPath('//a[@data-v-215483ec][@data-v-3d8f1a36]')->attr('href');
                $productImage = $crawler->filterXPath('//img[@data-v-3d8f1a36][contains(@src,"pimages")]')->attr('src');
                $productName = $crawler->filterXPath('//img[@data-v-3d8f1a36][contains(@src,"pimages")]')->attr('alt');
                $productIdentity = $this->extractProductIdentityWithURL($productURL); // example: 98703517

                // Check Product Data
                if ($productIdentity !== NULL && $productImage !== NULL && strlen($productName) > 0 && $productURL !== NULL) {

                    // User Crawler And Create Product
                    $myProduct = new Product();
                    $myProduct->setIdentity($productIdentity);
                    $myProduct->setName($productName);
                    $myProduct->setImage($productImage);
                    $myProduct->setUrl($productURL);

                    // Add Products
                    $extractedProducts->add($myProduct);

                }


            });

            // Flush Extracted Products
            $this->extractorHelper->pushProducts($extractedProducts, $myEvent->getMarketplace());

            // Flush Counts
            $this->extractorHelper->pushCounts($myEvent->getWebScrapingRequest(), $filteredProducts->count(), $extractedProducts->count());

        }

        // Extract Products With XHR Logs
        if (is_array($myXHRLog)) {
            // TODO : Extract Product With XHR
        }


    }

    private function extractProductIdentityWithURL(mixed $productURL): null|string
    {
        if (is_string($productURL)) {
            $productSlugDelimeter = '-p-';
            $parsedProductURLPath = parse_url($productURL, PHP_URL_PATH);
            if (is_string($parsedProductURLPath)) {
                $explodedParsedURL = explode($productSlugDelimeter, $parsedProductURLPath);
                if (count($explodedParsedURL) === 2) {
                    return $explodedParsedURL[array_key_last($explodedParsedURL)];
                }
            }
        }
        return NULL;
    }

}