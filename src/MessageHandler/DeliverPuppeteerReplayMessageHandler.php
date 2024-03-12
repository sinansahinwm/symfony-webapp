<?php

namespace App\MessageHandler;

use App\Message\DeliverPuppeteerReplayMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeliverPuppeteerReplayMessageHandler
{

    public function __invoke(DeliverPuppeteerReplayMessage $message)
    {
        // TODO : NODEJS IMPLEMENTATION
    }

}
