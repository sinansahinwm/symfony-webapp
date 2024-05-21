<?php namespace App\EventListener;

use App\Entity\User;
use App\Entity\UserPreferences;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'insertUserPreferencesIfNeeded', entity: User::class)]
class UserPreferencesListener
{

    public function insertUserPreferencesIfNeeded(User $persistedUsed, PrePersistEventArgs $myEvent): void
    {
        if ($persistedUsed->getUserPreferences() === NULL) {
            $persistedUsed->setUserPreferences(self::getDefaultUserPreferences($persistedUsed));
        }
    }

    public static function getDefaultUserPreferences(User $theUser): UserPreferences
    {
        // Create Preferences
        $myPreferences = new UserPreferences();
        $myPreferences->setUser($theUser);

        // Set Defaults
        $myPreferences->setReceiveEmails(TRUE);

        // Return
        return $myPreferences;
    }

}