<?php namespace App\ProductExtractor;

use App\Entity\Product;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'scraper.handle_extract_products.amazon.com.tr')]
class AmazonProductExtractor
{
    private ArrayCollection $extractedProducts;

    public function __construct(private WebScrapingRequestExtractorHelper $extractorHelper)
    {
        $this->extractedProducts = new ArrayCollection();
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
        $myProduct = new Product();
        $myProduct->setIdentity("SKU");
        $myProduct->setName("sdgsdgsd");
        $myProduct->setImage("sdgsdgsd");
        $myProduct->setUrl("https://example.com");

        $this->extractedProducts->add($myProduct);
        // TODO : Extract Amazon Products
    }

    private function extractProductsWithXhrLog(WebScrapingRequestExtractProductsEvent $extractEvent): void
    {
        // TODO : Extract Amazon Products
    }

}