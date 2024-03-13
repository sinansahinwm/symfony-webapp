<?php namespace App\Service\PuppeteerReplay;

use App\Message\NodeAppPackageDeliveryMessage;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class PuppeteerReplayService
{

    private string $webHookUrl = 'http://localhost';
    private string $instanceID = 'LOCALHOST';
    private int $timeOut = 7000;
    private array $puppeteerLaunchOptions = [];

    public function __construct(private PuppeteerReplayPackager $packager, private LoggerInterface $logger, private MessageBusInterface $messageBus)
    {
    }

    public function setOptions(string $webHookUrl, string $instanceID, int $timeOut = 7000, array $puppeteerLaunchOptions = []): self
    {
        $this->webHookUrl = $webHookUrl;
        $this->instanceID = $instanceID;
        $this->timeOut = $timeOut;
        $this->puppeteerLaunchOptions = $puppeteerLaunchOptions;
        return $this;
    }

    public function play(string $recordPath): Envelope|false
    {
        try {
            $puppeteerReplayPackage = $this->packager->loadRecord($recordPath)->package($this->webHookUrl, $this->instanceID, $this->timeOut, $this->puppeteerLaunchOptions);
            $deliverReplayNotification = new NodeAppPackageDeliveryMessage($puppeteerReplayPackage);
            return $this->messageBus->dispatch($deliverReplayNotification);
        } catch (Exception $exception) {
            $this->logger->error($exception);
        }
        return FALSE;
    }

}