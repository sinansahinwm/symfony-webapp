<?php namespace App\Message;

class PuppeteerReplayerDeliveryMessage
{

    public function __construct(private string $instanceID, private array $steps, private string $webhookUrl, private int $timeOut, private int $userID)
    {
    }

    public function getInstanceID(): string
    {
        return $this->instanceID;
    }

    public function setInstanceID(string $instanceID): void
    {
        $this->instanceID = $instanceID;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    public function setTimeOut(int $timeOut): void
    {
        $this->timeOut = $timeOut;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
    }

}