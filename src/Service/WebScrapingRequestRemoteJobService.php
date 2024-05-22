<?php namespace App\Service;

use App\Entity\WebScrapingRequest;
use App\EventListener\WebScrapingRequestListener;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebScrapingRequestRemoteJobService
{

    const PING_PONG_PATH = '/pingPong';
    const SCRAPER_PATH = '/firebaseScraper';

    public function __construct(private ContainerBagInterface $containerBag, private HttpClientInterface $httpClient)
    {
    }

    public function sendPingPong(): bool
    {
        $pingpongEndpoint = $this->getServerEndpoint() . self::PING_PONG_PATH;
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
        $scraperEndpoint = $this->getServerEndpoint() . self::SCRAPER_PATH;
        $myHttpClient = $this->prepareClient();

        try {

            // Send Request
            $myRequest = $myHttpClient->request('POST', $scraperEndpoint, [
                'json' => $this->getJsonBody($webScrapingRequest)
            ]);

            // Get Request Status Code
            $myRequestStatusCode = $myRequest->getStatusCode();

            if ($myRequestStatusCode === 200) {
                return TRUE;
            }

        } catch (TransportExceptionInterface $e) {
            return FALSE;
        }

        return FALSE;
    }

    private function getJsonBody(WebScrapingRequest $webScrapingRequest): array
    {
        return [
            'instanceID' => $webScrapingRequest->getId(),
            'webhookURL' => $webScrapingRequest->getWebhookUrl(),
            'navigateURL' => $webScrapingRequest->getNavigateUrl(),
        ];
    }

    private function getServerEndpoint(): string
    {
        return $this->containerBag->get("app.api_keys.firebase_scraper.endpoint");
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