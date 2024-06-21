<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.amazon.com.tr')]
class AmazonTurkeyProductExtractor
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
            $filteredProducts = $myCrawler->filterXPath('//div[@data-component-type="s-search-result"][@data-asin]');
            $filteredProducts->each(function (Crawler $crawler) use ($myEvent, $extractedProducts) {

                // Get Product Data
                $productIdentity = $crawler->attr('data-asin'); // example : B0BTVYBZ3P
                $productImage = $crawler->filterXPath('//span[@data-component-type="s-product-image"]//img[contains(@class,"s-image")]')->attr('src');
                $productName = $crawler->filterXPath('//div[contains(@class,"a-section")]//div[@data-cy="title-recipe"]//h2//a//span')->innerText();
                $productURL = $crawler->filterXPath('//div[contains(@class,"a-section")]//div[@data-cy="title-recipe"]//h2//a')->attr('href');

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

}