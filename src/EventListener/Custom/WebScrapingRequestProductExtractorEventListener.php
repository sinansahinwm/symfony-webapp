<?php namespace App\EventListener\Custom;

use App\Config\Event\ScraperCompletedEvents;
use App\Entity\Marketplace;
use App\Entity\WebScrapingRequest;
use App\MessageHandler\Event\WebScrapingRequestExtractProductsEvent;
use App\MessageHandler\HandleWebScrapingRequestAfterCompletedMessageHandler;
use App\Repository\MarketplaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ScraperCompletedEvents::HANDLE_EXTRACT_PRODUCTS)]
class WebScrapingRequestProductExtractorEventListener
{

    public function __construct(private MarketplaceRepository $marketplaceRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(WebScrapingRequest $myWebScrapingRequest)
    {
        $isExtractableMarketplace = $this->isExtractable($myWebScrapingRequest);

        if ($isExtractableMarketplace instanceof Marketplace) {
            $dispatcherEventName = self::getDispatcherEventName($myWebScrapingRequest, $isExtractableMarketplace);
            $dispatcherEvent = new WebScrapingRequestExtractProductsEvent($myWebScrapingRequest, $isExtractableMarketplace);
            $this->eventDispatcher->dispatch($dispatcherEvent, $dispatcherEventName);
        }
    }

    public static function getDispatcherEventName(WebScrapingRequest $webScrapingRequest, Marketplace $marketplace): string
    {
        $marketplaceHost = parse_url($marketplace->getUrl(), PHP_URL_HOST);
        $eventPrefix = HandleWebScrapingRequestAfterCompletedMessageHandler::getEventDispatcherEventName($webScrapingRequest);
        return $eventPrefix . "." . $marketplaceHost;
    }

    private function isExtractable(WebScrapingRequest $webScrapingRequest): Marketplace|false
    {
        $navigateURL = $webScrapingRequest->getNavigateUrl();
        $parsedNavigateURLHost = parse_url($navigateURL, PHP_URL_HOST);
        $marketplaces = $this->marketplaceRepository->findAll();
        foreach ($marketplaces as $marketplace) {
            $marketplaceURL = $marketplace->getUrl();
            $parsedMarketplaceURLHost = parse_url($marketplaceURL, PHP_URL_HOST);
            if (is_string($parsedNavigateURLHost) === TRUE && is_string($parsedMarketplaceURLHost) === TRUE && ($parsedMarketplaceURLHost === $parsedNavigateURLHost)) {
                return $this->dataIsExtractable($webScrapingRequest) === TRUE ? $marketplace : FALSE;
            }
        }
        return FALSE;
    }

    private function dataIsExtractable(WebScrapingRequest $webScrapingRequest): bool
    {
        $pageContent = $webScrapingRequest->getConsumedContent();
        $decodedPageContent = base64_decode($pageContent);
        $lastErrorMessage = $webScrapingRequest->getLastErrorMessage();
        return ($lastErrorMessage === NULL) && ($decodedPageContent !== FALSE) && ($pageContent !== NULL);
    }

}