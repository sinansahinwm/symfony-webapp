<?php namespace App\EventListener\Custom;

use App\Config\Event\ScraperEvents;
use App\Entity\WebScrapingRequest;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ScraperEvents::HANDLE_NULL)]
class WebScrapingRequestCompletedEventListener
{

    public function __invoke(WebScrapingRequest $myWebScrapingRequest)
    {
        // TODO : Null handle does not execute any action after scraping completes.
    }

}