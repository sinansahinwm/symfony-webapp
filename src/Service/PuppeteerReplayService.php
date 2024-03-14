<?php namespace App\Service;

use App\Controller\Webhook\BaseWebhook;
use App\Message\PuppeteerReplayerDeliveryMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class PuppeteerReplayService
{

    private string $recordPath;
    private string $webhookUrl;
    private string $instanceID;
    private int $timeOut = 7000;

    private BaseWebhook $webhook;

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function play(): Envelope|null
    {
        if ($this->checkReady() === TRUE) {
            $stepsArray = $this->readSteps();
            if (is_array($stepsArray)) {
                return $this->prepareReplayerEnvelope($stepsArray);
            }
        }
        return NULL;
    }

    private function prepareReplayerEnvelope(array $steps): Envelope
    {
        $myMessage = new PuppeteerReplayerDeliveryMessage($this->instanceID, $steps, $this->webhookUrl, $this->timeOut);
        return $this->messageBus->dispatch($myMessage);
    }

    private function readSteps(): bool|array
    {
        if (file_exists($this->recordPath) === TRUE) {
            $stepsContent = file_get_contents($this->recordPath);
            if ($stepsContent !== FALSE) {
                if (json_validate($stepsContent) === TRUE) {
                    $decodedJSON = json_decode($stepsContent, JSON_OBJECT_AS_ARRAY);
                    return $decodedJSON["steps"];
                }
            }
        }
        return FALSE;
    }

    private function checkReady(): bool
    {
        if ($this->timeOut < 1000) {
            return FALSE;
        }
        if (!str_starts_with(parse_url($this->webhookUrl, PHP_URL_SCHEME), 'http')) {
            return FALSE;
        }
        return TRUE;
    }

    public function getRecordPath(): string
    {
        return $this->recordPath;
    }

    public function setRecordPath(string $recordPath): self
    {
        $this->recordPath = $recordPath;
        return $this;
    }

    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(string $webhookUrl): self
    {
        $this->webhookUrl = $webhookUrl;
        return $this;
    }

    public function getInstanceID(): string
    {
        return $this->instanceID;
    }

    public function setInstanceID(string $instanceID): self
    {
        $this->instanceID = $instanceID;
        return $this;
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    public function setTimeOut(int $timeOut): self
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    public function getWebhook(): BaseWebhook
    {
        return $this->webhook;
    }

    public function setWebhook(BaseWebhook $webhook): self
    {
        $this->webhook = $webhook;
        $this->setWebhookUrl($webhook->getAbsoluteUrl());
        return $this;
    }


}