<?php namespace App\EventListener;

use App\Entity\PuppeteerReplay;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsEntityListener(event: Events::postPersist, method: "postPersist", entity: PuppeteerReplay::class)]
class PuppeteerReplayListener
{
    public function __construct(private StorageInterface $storage)
    {
    }

    public function postPersist(PuppeteerReplay $puppeteerReplay): void
    {

        $resolvedFilePath = $this->storage->resolvePath($puppeteerReplay);


        exit($resolvedFilePath);
    }

}