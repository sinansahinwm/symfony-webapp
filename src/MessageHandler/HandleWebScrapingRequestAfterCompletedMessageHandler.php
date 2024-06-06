<?php namespace App\MessageHandler;

use App\Entity\WebScrapingRequest;
use App\Message\HandleWebScrapingRequestAfterCompletedMessage;
use App\Repository\WebScrapingRequestRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class HandleWebScrapingRequestAfterCompletedMessageHandler
{

    public function __construct(private WebScrapingRequestRepository $webScrapingRequestRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(HandleWebScrapingRequestAfterCompletedMessage $myMessage)
    {
        $myWebScrapingRequest = $this->webScrapingRequestRepository->find($myMessage->getWebScrapingRequestID());

        if ($myWebScrapingRequest instanceof WebScrapingRequest) {


            $myHandle = $myWebScrapingRequest->getCompletedHandle();
            $this->eventDispatcher->dispatch($myWebScrapingRequest, 'scraper.' . strtolower($myHandle));

        }

    }

}