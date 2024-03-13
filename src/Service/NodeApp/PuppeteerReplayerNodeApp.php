<?php namespace App\Service\NodeApp;

use App\Message\NodeAppPackageDeliveryMessage;
use App\Service\NodeApp\PuppeteerReplayer\PuppeteerReplayCombinator;
use App\Service\NodeApp\PuppeteerReplayer\PuppeteerReplayLoader;
use Symfony\Component\Messenger\MessageBusInterface;

class PuppeteerReplayerNodeApp implements NodeAppInterface
{

    private string $recordPath;
    private string $webHookUrl;
    private string $instanceID;
    private int $timeOut;
    private array $puppeteerLaunchOptions;

    public function __construct(private MessageBusInterface $messageBus, private NodeAppPackagerService $nodeAppPackagerService, private PuppeteerReplayLoader $puppeteerReplayLoader, private PuppeteerReplayCombinator $puppeteerReplayCombinator)
    {
    }

    public function setRecordPath(string $recordPath): self
    {
        $this->recordPath = $recordPath;
        return $this;
    }

    public function setOptions(string $webHookUrl, string $instanceID, int $timeOut = 700, array $puppeteerLaunchOptions = []): self
    {
        $this->webHookUrl = $webHookUrl;
        $this->instanceID = $instanceID;
        $this->timeOut = $timeOut;
        $this->puppeteerLaunchOptions = $puppeteerLaunchOptions;
        return $this;
    }

    public static function getName(): string
    {
        return "puppeteer_replayer";
    }

    public function getEntrypointContent(): string
    {
        $combinedContent = $this->puppeteerReplayCombinator->setLoader($this->puppeteerReplayLoader->loadRecord($this->recordPath))->combineWith($this->webHookUrl, $this->instanceID, $this->timeOut, $this->puppeteerLaunchOptions);
        return $combinedContent;
    }

    public function releaseApp(): void
    {
        $nodeAppPackage = $this->nodeAppPackagerService->packageApp(self::getName(), $this->getEntrypointContent());
        $nodeAppDeliverMessage = new NodeAppPackageDeliveryMessage($nodeAppPackage);
        $this->messageBus->dispatch($nodeAppDeliverMessage);
    }

}