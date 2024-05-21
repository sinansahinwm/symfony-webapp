<?php namespace App\EventListener;

use App\Config\PuppeteerReplayStatusType;
use App\Entity\AbstractFile;
use App\Entity\PuppeteerReplay;
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

        if ($object instanceof AbstractFile) {
            $object->setUploadedAt(new DateTimeImmutable());
            $object->setUploadedBy($this->security->getUser());
        }

    }

}