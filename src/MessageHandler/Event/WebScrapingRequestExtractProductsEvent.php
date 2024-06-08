<?php namespace App\MessageHandler\Event;

use App\Entity\Marketplace;
use App\Entity\WebScrapingRequest;

class WebScrapingRequestExtractProductsEvent
{
    public function __construct(private WebScrapingRequest $webScrapingRequest, private Marketplace $marketplace)
    {
    }

    public function getWebScrapingRequest(): WebScrapingRequest
    {
        return $this->webScrapingRequest;
    }

    public function setWebScrapingRequest(WebScrapingRequest $webScrapingRequest): void
    {
        $this->webScrapingRequest = $webScrapingRequest;
    }

    public function getMarketplace(): Marketplace
    {
        return $this->marketplace;
    }

    public function setMarketplace(Marketplace $marketplace): void
    {
        $this->marketplace = $marketplace;
    }

}