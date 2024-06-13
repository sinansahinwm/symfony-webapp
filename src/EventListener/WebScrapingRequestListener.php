<?php namespace App\EventListener;

use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use App\Message\ProccessWebScrapingRequestMessage;
use App\RemoteEvent\FirebaseScraperWebhookConsumer;
use App\Repository\WebScrapingRequestRepository;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postPersist, method: 'webScrapingRequestPostPersist', entity: WebScrapingRequest::class)]
class WebScrapingRequestListener
{

    const CACHE_TIME_MINUTES = 30;
    const DEFAULT_SCRAPER_WEBHOOK_ROUTE = "firebase_scraper";

    public function __construct(
        private MessageBusInterface            $messageBus,
        private UrlGeneratorInterface          $urlGenerator,
        private EntityManagerInterface         $entityManager,
        private WebScrapingRequestRepository   $webScrapingRequestRepository,
        private FirebaseScraperWebhookConsumer $firebaseScraperWebhookConsumer
    )
    {
    }

    public function webScrapingRequestPostPersist(WebScrapingRequest $webScrapingRequest, PostPersistEventArgs $myEvent): void
    {

        // If Newly Created -> Send To Proccess
        if ($webScrapingRequest->getStatus() === WebScrapingRequestStatusType::NEWLY_CREATED) {

            // Check Cache Exist
            $cacheExist = $this->requestCacheExist($webScrapingRequest);

            if ($cacheExist !== FALSE) {

                // Use Cached Request
                $this->addCachedRequest($webScrapingRequest, $cacheExist);

            } else {

                // Request New
                $webhookURL = $this->prepareWebhookUrlForWebScrapingRequest();
                $webScrapingRequest->setWebhookUrl($webhookURL);
                $this->entityManager->persist($webScrapingRequest);
                $this->entityManager->flush();
                $myMessage = new ProccessWebScrapingRequestMessage($webScrapingRequest->getId());
                $this->messageBus->dispatch($myMessage);

            }


        }

    }

    private function addCachedRequest(WebScrapingRequest $requestedObject, WebScrapingRequest $cachedObject): void
    {

        // Copy Data to Requested Object
        $requestedObject->setWebhookUrl($cachedObject->getWebhookUrl());
        $requestedObject->setStatus($cachedObject->getStatus());
        $requestedObject->setConsumedScreenshot($cachedObject->getConsumedScreenshot());
        $requestedObject->setConsumedContent($cachedObject->getConsumedContent());
        $requestedObject->setConsumedUrl($cachedObject->getConsumedUrl());
        $requestedObject->setConsumedRemoteStatus($cachedObject->getConsumedRemoteStatus());
        $requestedObject->setCompletedHandle($cachedObject->getCompletedHandle());
        $requestedObject->setSteps($cachedObject->getSteps());
        $requestedObject->setConsumedAt(new DateTimeImmutable());
        $requestedObject->setLastErrorMessage($cachedObject->getLastErrorMessage());
        $requestedObject->setXhrlog($cachedObject->getXhrlog());

        // Persist Requested Object
        $this->entityManager->persist($requestedObject);
        $this->entityManager->flush();

        // Handle If Completed
        $this->firebaseScraperWebhookConsumer->handleWebScrapingRequestIfCompleted($requestedObject);

    }

    private function prepareWebhookUrlForWebScrapingRequest(): string
    {
        return $this->urlGenerator->generate('_webhook_controller', ['type' => self::DEFAULT_SCRAPER_WEBHOOK_ROUTE], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    private function requestCacheExist(WebScrapingRequest $webScrapingRequest): false|WebScrapingRequest
    {
        $myCachedRequest = $this->webScrapingRequestRepository->findOneBy([
            'navigate_url' => $webScrapingRequest->getNavigateUrl(),
            'status' => WebScrapingRequestStatusType::COMPLETED,
            'steps' => $webScrapingRequest->getSteps(),
        ]);

        if ($myCachedRequest instanceof WebScrapingRequest) {
            $timeNow = new DateTimeImmutable();
            $myCachedRequestCreatedAt = $myCachedRequest->getCreatedAt()->getTimestamp();
            $timeDiffMin = ($timeNow->getTimestamp() - $myCachedRequestCreatedAt) / 60;
            if ($timeDiffMin < self::CACHE_TIME_MINUTES) {
                return $myCachedRequest;
            }
        }

        return FALSE;
    }


}