<?php

namespace App\MessageHandler;

use App\Message\PuppeteerReplayerDeliveryMessage;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AsMessageHandler]
final class PuppeteerReplayerDeliveryMessageHandler
{

    const DEFAULT_REQUEST_METHOD = 'POST';
    const DEFAULT_LOCALHOST_URL = 'http://127.0.0.1';
    const DEFAULT_LOCALHOST_PORT = 3030;

    const PUPPETEER_LAUNCH_OPTIONS = [
        "headless" => TRUE,
    ];

    public function __construct(private HttpClientInterface $httpClient, private LoggerInterface $logger, private ContainerBagInterface $containerBag)
    {
    }

    public function __invoke(PuppeteerReplayerDeliveryMessage $message)
    {
        $myRequest = $this->createRequestForSteps($message);
        if ($myRequest instanceof ResponseInterface) {
            try {
                $requestStatusCode = $myRequest->getStatusCode();
                if ($requestStatusCode !== 200) {
                    $requestContent = $myRequest->getContent(FALSE);
                    $this->logger->error($requestContent);
                }
            } catch (TransportExceptionInterface|ClientExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface $e) {
                $this->logger->error($e);
            }
        }


    }

    private function createRequestForSteps(PuppeteerReplayerDeliveryMessage $message): ResponseInterface|null
    {
        try {
            $requestURL = $this->getServerRequestURL();
            $requestOptions = $this->getRequestOptions($message);
            return $this->httpClient->request(self::DEFAULT_REQUEST_METHOD, $requestURL, $requestOptions);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error($exception);
        }
        return NULL;
    }

    private function getServerRequestURL(): string
    {
        return self::DEFAULT_LOCALHOST_URL . ":" . self::DEFAULT_LOCALHOST_PORT;
    }

    private function getRequestOptions(PuppeteerReplayerDeliveryMessage $message): array
    {
        $requestOptions = [];


        [$authHeaderKey, $authHeaderValue] = $this->getAuthHeader();

        // Add Headers
        $requestOptions["headers"] = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            $authHeaderKey => $authHeaderValue,
        ];

        // Add JSON
        $requestOptions['json'] = $this->getRequestJSONBody($message);

        return $requestOptions;
    }

    private function getRequestJSONBody(PuppeteerReplayerDeliveryMessage $message): array
    {
        $preparedBodyJson = [];
        $preparedBodyJson["instanceID"] = $message->getInstanceID();
        $preparedBodyJson["webhookURL"] = $message->getWebhookUrl();
        $preparedBodyJson["timeOut"] = $message->getTimeOut();
        $preparedBodyJson["puppeteerLaunchOptions"] = self::PUPPETEER_LAUNCH_OPTIONS;
        $preparedBodyJson["steps"] = $message->getSteps();
        return $preparedBodyJson;
    }

    private function getAuthHeader(): array
    {
        return [$this->containerBag->get("app.cloud_functions.auth_header"), $this->containerBag->get("app.cloud_functions.auth_secret")];
    }
}
