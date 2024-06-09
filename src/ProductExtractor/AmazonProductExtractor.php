<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.amazon.com.tr')]
class AmazonProductExtractor
{

    public function __construct(private WebScrapingRequestExtractorHelper $extractorHelper)
    {
    }

    public function __invoke(WebScrapingRequestExtractProductsEvent $myEvent): void
    {

        // Get Crawler & XHR Logs
        $myCrawler = $this->extractorHelper->getCrawler($myEvent->getWebScrapingRequest(), $myEvent->getMarketplace());
        $myXHRLog = $this->extractorHelper->getXHRLog($myEvent->getWebScrapingRequest(), $myEvent->getMarketplace());

        // User Crawler And Create Product
        $myProduct = new Product();
        $myProduct->setIdentity("EXAMPLE_PRODUCT");
        $myProduct->setName("Example Product");
        $myProduct->setImage("https://placehold.co/400x400/jpg");
        $myProduct->setUrl("https://example.com");

        // Push Created Product
        $pushedProductOrFalse = $this->extractorHelper->pushProduct($myProduct, $myEvent->getMarketplace());

    }

}