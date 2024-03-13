<?php namespace App\Service\NodeApp;

use App\Message\NodeAppPackageDeliveryMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class NodeAppPackageReleaseManager
{
    public function __construct(private NodeAppPackagerService $nodeAppPackagerService, private MessageBusInterface $messageBus)
    {
    }

    public function releaseApp(NodeAppInterface $nodeApp): void
    {
        $nodeAppPackage = $this->nodeAppPackagerService->packageApp($nodeApp->getName(), $nodeApp->getEntrypointContent());
        $nodeAppDeliverMessage = new NodeAppPackageDeliveryMessage($nodeAppPackage);
        $this->messageBus->dispatch($nodeAppDeliverMessage, [new DelayStamp(100000)]);
    }
}