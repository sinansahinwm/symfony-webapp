<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.trendyol.com')]
class TrendyolProductExtractor
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
            $myCrawler->filterXPath('//div[@class="prdct-cntnr-wrppr"]//div[contains(@class,"p-card-wrppr")][@data-id]')->each(function (Crawler $crawler) use ($myEvent, $extractedProducts) {

                // Get Product Data
                $productIdentity = $crawler->attr('data-id');
                $productImage = $crawler->filterXPath('//img[contains(@class,"p-card-img")]')->attr('src');
                $_productBrand = $crawler->filterXPath('//div[contains(@class,"prdct-desc-cntnr")]//h3//span[contains(@class,"prdct-desc-cntnr-ttl")]')->innerText();
                $_productName = $crawler->filterXPath('//div[contains(@class,"prdct-desc-cntnr")]//h3//span[contains(@class,"prdct-desc-cntnr-name")]')->innerText();
                $_productDesc = $crawler->filterXPath('//div[contains(@class,"prdct-desc-cntnr")]//h3//div[contains(@class,"product-desc-sub-container")]//div[contains(@class,"product-desc-sub-text")]')->innerText();
                $productName = implode(' ', [trim($_productBrand), trim($_productName), trim($_productDesc)]);
                $productURL = $crawler->filterXPath('//a')->attr('href');

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