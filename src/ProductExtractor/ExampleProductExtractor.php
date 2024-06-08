<?php namespace App\ProductExtractor;

use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.example.com.tr')]
class ExampleProductExtractor
{
    private ArrayCollection $extractedProducts;

    public function __construct(private WebScrapingRequestExtractorHelper $extractorHelper)
    {
    }

    public function __invoke(WebScrapingRequestExtractProductsEvent $myEvent): void
    {

        // Run Product Extractors
        $this->extractProductsWithDOM($myEvent);
        $this->extractProductsWithXhrLog($myEvent);

        // Add Extracted Products If Exist
        $this->extractorHelper->flushExtractedProducts($this->extractedProducts, $myEvent->getMarketplace());

    }

    private function extractProductsWithDOM(WebScrapingRequestExtractProductsEvent $extractEvent): void
    {
        // TODO : Extract Products Using DOM Content
    }

    private function extractProductsWithXhrLog(WebScrapingRequestExtractProductsEvent $extractEvent): void
    {
        // TODO : Extract Products Using XHR Logs
    }

}