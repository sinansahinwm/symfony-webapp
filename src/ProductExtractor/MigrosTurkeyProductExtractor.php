<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.migros.com.tr')]
class MigrosTurkeyProductExtractor
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
            $myCrawler->filterXPath('//fe-product-image[@id="product-image"]')->each(function (Crawler $crawler) use ($myEvent, $extractedProducts) {

                // Get Product Data
                $productURL = $crawler->filterXPath('//a[@id="product-image-link"]')->attr('href');
                $productImage = $crawler->filterXPath('//img[1][@felazyload]')->attr('src');
                $productName = $crawler->filterXPath('//img[1][@felazyload]')->attr('alt');
                $productIdentity = $this->extractProductIdentityWithURL($productURL);

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

        }

        // Extract Products With XHR Logs
        if (is_array($myXHRLog)) {
            // TODO : Extract Product With XHR
        }


    }

    private function extractProductIdentityWithURL(mixed $productURL): null|string
    {
        if (is_string($productURL)) {
            $parsedProductURLPath = parse_url($productURL, PHP_URL_PATH);
            return str_replace(['/'], [''], $parsedProductURLPath);
        }
        return NULL;
    }

}