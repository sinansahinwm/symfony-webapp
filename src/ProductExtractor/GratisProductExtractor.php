<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.gratis.com')]
class GratisProductExtractor
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
            $myCrawler->filterXPath('//div[contains(@class,"product-list-wrapper")]//app-custom-product-grid-item')->each(function (Crawler $crawler) use ($myEvent, $extractedProducts) {

                // Check Placeholder Exist
                $crawlerPlaceholderExist = str_contains($crawler->outerHtml(), 'gratis-placeholder.svg');
                if ($crawlerPlaceholderExist === TRUE) {
                    return;
                }

                // Get Product Data
                $productName = $crawler->filterXPath('//div[contains(@class,"infos")]//a[contains(@class,"cx-product-name")]//h5[contains(@class,"title")]')->innerText() ?? NULL;
                $productImage = $crawler->filterXPath('//div[contains(@class,"view")]//img[not(contains(@src,"gratis-placeholder.svg"))]')->attr('src') ?? NULL;
                $productURL = $crawler->filterXPath('//div[contains(@class,"view")]//a[contains(@class,"product-image-for-grid-item")]')->attr('href') ?? NULL;
                $productIdentity = $crawler->filterXPath('//app-custom-add-to-cart//form[contains(@class,"add-to-cart-form")][@product-code]')->attr('product-code') ?? NULL;

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