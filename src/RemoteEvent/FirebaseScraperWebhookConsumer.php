<?php

namespace App\RemoteEvent;

use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('firebase_scraper')]
final class FirebaseScraperWebhookConsumer implements ConsumerInterface
{
    public function __construct()
    {
    }

    public function consume(RemoteEvent $myRemoteEvent): void
    {
        $eventPayload = $myRemoteEvent->getPayload();
        // TODO : consuming
    }
}
