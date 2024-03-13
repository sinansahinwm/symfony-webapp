<?php

namespace App\MessageHandler;

use App\Message\NodeAppPackageDeliveryMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NodeAppPackageDeliveryMessageHandler
{

    public function __invoke(NodeAppPackageDeliveryMessage $message)
    {
        // TODO : NODEJS IMPLEMENTATION
    }

}
