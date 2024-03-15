<?php

namespace App\Security\Voter;

use App\Entity\Notification;
use App\Entity\PuppeteerReplay;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationVoter extends Voter
{

    public const NOTIFICATION_MARK_AS_READ = 'NOTIFICATION_MARK_AS_READ';
    public const NOTIFICATION_DELETE = 'NOTIFICATION_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::NOTIFICATION_MARK_AS_READ, self::NOTIFICATION_DELETE]) && $subject instanceof PuppeteerReplay;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Check For Anonymous Login
        if (!$user instanceof UserInterface) {
            return FALSE;
        }

        // Check For Admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return TRUE;
        }

        $subjectNotification = $subject;

        // Check For Other
        return match ($attribute) {
            self::NOTIFICATION_MARK_AS_READ => $this->canMarkAsRead($subjectNotification, $user),
            self::NOTIFICATION_DELETE => $this->canDelete($subjectNotification, $user),
            default => FALSE
        };

    }

    private function canMarkAsRead(Notification $userNotification, User|UserInterface $loggedUser): bool
    {
        if ($loggedUser->getUserIdentifier() == $userNotification->getToUser()->getUserIdentifier()) {
            return TRUE;
        }
        return FALSE;
    }

    private function canDelete(Notification $userNotification, User|UserInterface $loggedUser): bool
    {
        if ($loggedUser->getUserIdentifier() == $userNotification->getToUser()->getUserIdentifier()) {
            return TRUE;
        }
        return FALSE;
    }

}
