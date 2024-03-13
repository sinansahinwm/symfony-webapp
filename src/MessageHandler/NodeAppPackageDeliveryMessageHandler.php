<?php

namespace App\MessageHandler;

use App\Message\NodeAppPackageDeliveryMessage;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NodeAppPackageDeliveryMessageHandler
{

    public function __invoke(NodeAppPackageDeliveryMessage $message)
    {
        $packagePath = $message->getPackagePath();
        if (file_exists($packagePath) === FALSE) {
            throw new FileNotFoundException();
        }

        // TODO : NODEJS IMPLEMENTATION
    }

}
