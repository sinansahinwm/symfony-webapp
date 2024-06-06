<?php namespace App\EventListener\Custom;

use App\Config\Event\ScraperCompletedEvents;
use App\Entity\WebScrapingRequest;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ScraperCompletedEvents::HANDLE_NULL)]
class WebScrapingRequestCompletedEventListener
{

    public function __invoke(WebScrapingRequest $myWebScrapingRequest)
    {

    }

}