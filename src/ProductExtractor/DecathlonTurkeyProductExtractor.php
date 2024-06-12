<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.decathlon.com.tr')]
class DecathlonTurkeyProductExtractor
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
            $myCrawler->filterXPath('//div[contains(@class,"product-list")]//div[@role="listitem"]')->each(function (Crawler $crawler) use ($myEvent, $extractedProducts) {

                // Get Product Data
                $productIdentity = $crawler->attr('data-supermodelid');
                $productURL = $crawler->filterXPath('//a[contains(@class,"dpb-product-model-link")]')->attr('href');
                $productImage = $crawler->filterXPath('//div[contains(@class,"dpb-models")]//img[@width][@height]')->attr('src');

                $_productBrand = $crawler->filterXPath('//a[contains(@class,"vtmn-block")]//strong')->innerText();
                $_productName = $crawler->filterXPath('//a[contains(@class,"vtmn-block")]//h2')->innerText();
                $productName = $_productBrand . " " . $_productName;

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

}