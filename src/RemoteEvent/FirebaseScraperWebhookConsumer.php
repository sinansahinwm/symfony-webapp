<?php

namespace App\RemoteEvent;

use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use App\Repository\WebScrapingRequestRepository;
use App\Service\WebScrapingRequestRemoteJobService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('firebase_scraper')]
final class FirebaseScraperWebhookConsumer implements ConsumerInterface
{
    public function __construct(private WebScrapingRequestRepository $webScrapingRequestRepository, private EntityManagerInterface $entityManager)
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
            if ($consumedWebScrapingRequest === NULL) {
                $myWebScrapingRequest->setStatus(WebScrapingRequestStatusType::CONSUME_ERROR);
                $this->entityManager->persist($myWebScrapingRequest);
                $this->entityManager->flush();
            } else {
                $myWebScrapingRequest = $consumedWebScrapingRequest;
            }

            // Set Status To Completed
            $myWebScrapingRequest->setStatus(WebScrapingRequestStatusType::COMPLETED);
            $this->entityManager->persist($myWebScrapingRequest);
            $this->entityManager->flush();
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

            // Push Consumed Data
            $decodedContent = base64_decode($myPayloadContent);
            $webScrapingRequest->setConsumedRemoteStatus($myPayloadStatus);
            $webScrapingRequest->setConsumedScreenshot($myPayloadScreenshot);
            $webScrapingRequest->setConsumedUrl($myPayloadUrl);
            $webScrapingRequest->setConsumedContent($decodedContent);

            return $webScrapingRequest;

        } catch (Exception $exception) {
            return NULL;
        }
    }


}
