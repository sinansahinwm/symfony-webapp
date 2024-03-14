<?php namespace App\EventListener;

use App\Config\PuppeteerReplayStatusType;
use Vich\UploaderBundle\Event\Event;

class PuppeteerReplayListener
{
    public function onVichUploaderPreUpload(Event $event): void
    {
        $object = $event->getObject();
        $object->setStatus(PuppeteerReplayStatusType::UPLOAD);
    }

}