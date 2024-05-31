<?php

namespace App\RemoteEvent;

use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use App\Message\HandleWebScrapingRequestAfterCompletedMessage;
use App\Repository\WebScrapingRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('firebase_scraper')]
final class FirebaseScraperWebhookConsumer implements ConsumerInterface
{
    public function __construct(private WebScrapingRequestRepository $webScrapingRequestRepository, private EntityManagerInterface $entityManager, private MessageBusInterface $messageBus)
    {
    }

    public function consume(RemoteEvent $myRemoteEvent): void
    {
        // Get Payload
        $remoteEventPayload = $myRemoteEvent->getPayload();

        // Get Payload Data
        $myPayloadInstanceID = $remoteEventPayload["instanceID"];

        // Find Object
        $myWebScrapingRequest = $this->webScrapingRequestRepository->find($myPayloadInstanceID);

        if ($myWebScrapingRequest) {

            // Set Status To Consuming
            $myWebScrapingRequest->setStatus(WebScrapingRequestStatusType::CONSUMING);
            $this->entityManager->persist($myWebScrapingRequest);
            $this->entityManager->flush();

            // Consume Payload
            $consumedWebScrapingRequest = $this->consumePayload($remoteEventPayload, $myWebScrapingRequest);

            if ($consumedWebScrapingRequest instanceof WebScrapingRequest) {
                $myWebScrapingRequest = $consumedWebScrapingRequest;
                $myWebScrapingRequest->setStatus(WebScrapingRequestStatusType::COMPLETED);
            } else {
                $myWebScrapingRequest->setStatus(WebScrapingRequestStatusType::CONSUME_ERROR);
            }

            // Persist WebScrapingRequest
            $this->entityManager->persist($myWebScrapingRequest);
            $this->entityManager->flush();

            // Handle
            $this->handleWebScrapingRequestIfCompleted($myWebScrapingRequest);

        }

    }

    public function handleWebScrapingRequestIfCompleted(WebScrapingRequest $webScrapingRequest): void
    {
        // Handle After Completed
        if ($webScrapingRequest->getStatus() === WebScrapingRequestStatusType::COMPLETED) {
            $myHandleMessage = new HandleWebScrapingRequestAfterCompletedMessage($webScrapingRequest->getId());
            $this->messageBus->dispatch($myHandleMessage);
        }
    }

    private function consumePayload(array $remoteEventPayload, WebScrapingRequest $webScrapingRequest): WebScrapingRequest|null
    {
        try {

            $myPayloadInstanceID = $remoteEventPayload["instanceID"];
            $myPayloadScreenshot = $remoteEventPayload["screenshot"];
            $myPayloadContent = $remoteEventPayload["content"];
            $myPayloadUrl = $remoteEventPayload["url"];
            $myPayloadStatus = $remoteEventPayload["status"];

            // Check Remote Status
            if ($myPayloadStatus !== 200) {
                $webScrapingRequest->setStatus(WebScrapingRequestStatusType::REMOTE_STATUS_FAILED_WHEN_CONSUMING);
            }

            // Put Content To File
            $decodedContent = base64_decode($myPayloadContent);

            if ($decodedContent === FALSE) {
                $webScrapingRequest->setStatus(WebScrapingRequestStatusType::FAILED_TO_PUT_CONTENT_WHEN_CONSUMING);
            } else {
                $webScrapingRequest->setConsumedContent($myPayloadContent);
            }

            // Push Other
            $webScrapingRequest->setConsumedRemoteStatus($myPayloadStatus);
            $webScrapingRequest->setConsumedScreenshot($myPayloadScreenshot);
            $webScrapingRequest->setConsumedUrl($myPayloadUrl);


            return $webScrapingRequest;

        } catch (Exception $exception) {
            return NULL;
        }
    }

}
