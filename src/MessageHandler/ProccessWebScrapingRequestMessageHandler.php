<?php

namespace App\MessageHandler;

use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use App\Message\ProccessWebScrapingRequestMessage;
use App\Repository\WebScrapingRequestRepository;
use App\Service\WebScrapingRequestRemoteJobService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler]
final class ProccessWebScrapingRequestMessageHandler
{

    public function __construct(private WebScrapingRequestRepository $webScrapingRequestRepository, private WebScrapingRequestRemoteJobService $webScrapingRequestRemoteJobService, private TranslatorInterface $translator, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(ProccessWebScrapingRequestMessage $myMessage)
    {

        $throwMessageError = NULL;

        // Get Message Object
        $myWebScrapingRequest = $this->webScrapingRequestRepository->find($myMessage->getWebScrapingRequestID());

        // Check Message Object
        if ($myWebScrapingRequest instanceof WebScrapingRequest) {

            // Ping Remote Server
            $pingPongSuccess = $this->webScrapingRequestRemoteJobService->sendPingPong();

            if ($pingPongSuccess === TRUE) {

                // Set New Status
                $myWebScrapingRequest->setStatus(WebScrapingRequestStatusType::FORWARDED_TO_REMOTE_SERVER);
                $this->entityManager->persist($myWebScrapingRequest);

                // Send Scraping Request to Remote Server
                $sendToRemoteServerResult = $this->webScrapingRequestRemoteJobService->sendToRemoteServer($myWebScrapingRequest);

                // If Sending Failed Throw Error & Retry Message
                if ($sendToRemoteServerResult === FALSE) {
                    $throwMessageError = TRUE;
                }

            } else {

                // Set New Status
                $myWebScrapingRequest->setStatus(WebScrapingRequestStatusType::PING_PONG_FAILED);
                $this->entityManager->persist($myWebScrapingRequest);

                // Throw Error When PingPong Failed
                $throwMessageError = TRUE;

            }


            // Flush Object
            $this->entityManager->flush();

            // Throw Error If Needed
            if ($throwMessageError === TRUE) {
                throw new Exception($this->translator->trans("Veri çekme isteği başarısız oldu."));
            }

        }

    }

}
