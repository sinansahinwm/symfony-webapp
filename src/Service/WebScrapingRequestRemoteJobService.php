<?php namespace App\Service;

use App\Entity\WebScrapingRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebScrapingRequestRemoteJobService
{

    public function __construct(private ContainerBagInterface $containerBag, private HttpClientInterface $httpClient, private EntityManagerInterface $entityManager)
    {
    }

    public function sendPingPong(): bool
    {
        $pingpongEndpoint = $this->getPingPongEndpoint();
        $myHttpClient = $this->prepareClient();
        try {
            $myPingPongResponse = $myHttpClient->request(
                'GET',
                $pingpongEndpoint
            );
            $myStatusCode = $myPingPongResponse->getStatusCode();
            return $myStatusCode === 200;
        } catch (TransportExceptionInterface $e) {
            return FALSE;
        }
    }


    public function sendToRemoteServer(WebScrapingRequest $webScrapingRequest): bool
    {
        $scraperEndpoint = $this->getServerEndpoint();
        $myHttpClient = $this->prepareClient();

        try {

            // Send Request
            $myRequest = $myHttpClient->request('POST', $scraperEndpoint, [
                'json' => $this->getJsonBody($webScrapingRequest),
                'timeout' => $this->getAllowedTimeout()
            ]);

            // Get Request Status Code
            $myRequestStatusCode = $myRequest->getStatusCode();
            if ($myRequestStatusCode === 200) {
                return TRUE;
            } else {
                $this->setLastErrorMessage($webScrapingRequest, "INVALID STATUS CODE " . $myRequestStatusCode);
            }

        } catch (TransportExceptionInterface $e) {
            $this->setLastErrorMessage($webScrapingRequest, $e->getMessage());
            return FALSE;
        }

        return FALSE;
    }


    private function setLastErrorMessage(WebScrapingRequest $webScrapingRequest, string $errorMessage): void
    {
        $webScrapingRequest->setLastErrorMessage($errorMessage);
        $this->entityManager->persist($webScrapingRequest);
        $this->entityManager->flush();
    }

    private function getJsonBody(WebScrapingRequest $webScrapingRequest): array
    {
        $payloadData = [
            'instanceID' => $webScrapingRequest->getId(),
            'webhookURL' => $webScrapingRequest->getWebhookUrl(),
            'navigateURL' => $webScrapingRequest->getNavigateUrl(),
            'puppeteerLaunchOptions' => $this->getLaunchOptions($webScrapingRequest),
        ];

        // Add Steps If Exist
        if ($webScrapingRequest->getSteps() !== NULL) {
            $payloadData['steps'] = json_decode($webScrapingRequest->getSteps());
        }

        return $payloadData;
    }

    private function getLaunchOptions(WebScrapingRequest $webScrapingRequest): array
    {
        $myLaunchOptions = [];
        // TODO : Add Launch Options If Needed
        return $myLaunchOptions;
    }

    private function getAllowedTimeout(): string
    {
        return (int)$this->containerBag->get("app.api_keys.firebase_scraper.timeout");
    }

    private function getServerEndpoint(): string
    {
        return $this->containerBag->get("app.api_keys.firebase_scraper.endpoint");
    }

    private function getPingPongEndpoint(): string
    {
        return $this->containerBag->get("app.api_keys.firebase_scraper.pingpong_endpoint");
    }

    private function getServerSecret(): string
    {
        return $this->containerBag->get("app.api_keys.firebase_scraper.secret");
    }

    private function prepareClient(): HttpClientInterface
    {
        $theClient = $this->httpClient;
        return $theClient->withOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getServerSecret(),
                'Content-Type' => 'application/json'
            ],
        ]);

    }
}