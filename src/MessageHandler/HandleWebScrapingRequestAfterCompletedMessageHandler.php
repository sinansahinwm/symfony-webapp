<?php namespace App\MessageHandler;

use App\Config\WebScrapingRequestCompletedHandleType;
use App\Entity\WebScrapingRequest;
use App\Message\HandleWebScrapingRequestAfterCompletedMessage;
use App\Repository\WebScrapingRequestRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class HandleWebScrapingRequestAfterCompletedMessageHandler
{

    public function __construct(private WebScrapingRequestRepository $webScrapingRequestRepository)
    {
    }

    public function __invoke(HandleWebScrapingRequestAfterCompletedMessage $myMessage)
    {
        $myWebScrapingRequest = $this->webScrapingRequestRepository->find($myMessage->getWebScrapingRequestID());

        if ($myWebScrapingRequest instanceof WebScrapingRequest) {

            $myHandle = $myWebScrapingRequest->getCompletedHandle();

            // If Handle Is Null, No Action Required
            if ($myHandle === WebScrapingRequestCompletedHandleType::HANDLE_NULL) {
                return;
            }

            // TODO : Handle Completed Requests
        }

    }

}