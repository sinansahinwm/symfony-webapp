<?php namespace App\Service\NodeApp\PuppeteerReplayer;

use App\Message\NodeAppPackageDeliveryMessage;
use App\Service\NodeApp\NodeAppInterface;
use App\Service\NodeApp\NodeAppPackagerService;
use App\Service\NodeApp\NodeAppService;
use Symfony\Component\Messenger\MessageBusInterface;

class PuppeteerReplayerNodeApp extends NodeAppService implements NodeAppInterface
{

    public function __construct(private MessageBusInterface $messageBus, private NodeAppPackagerService $nodeAppPackagerService)
    {
    }

    public static function getName(): string
    {
        return "puppeteer_replayer";
    }

    public function getEntrypointContent(): string
    {
        return "// This is the apps page content...";
    }

    public function releaseApp(): void
    {
        $nodeAppPackage = $this->nodeAppPackagerService->packageApp(self::getName(), $this->getEntrypointContent());
        $nodeAppDeliverMessage = new NodeAppPackageDeliveryMessage($nodeAppPackage);
        $this->messageBus->dispatch($nodeAppDeliverMessage);
    }

}