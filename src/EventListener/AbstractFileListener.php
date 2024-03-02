<?php namespace App\EventListener;

use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Vich\UploaderBundle\Event\Event;

class AbstractFileListener
{

    public function __construct(private Security $security)
    {
    }

    public function onVichUploaderPreUpload(Event $event): void
    {
        $object = $event->getObject();
        $object->setUploadedAt(new DateTimeImmutable());
        $object->setUploadedBy($this->security->getUser());
    }

}