<?php

namespace App\Security\Voter;

use App\Entity\PuppeteerReplay;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PuppeteerReplayVoter extends Voter
{

    public const PUPPETEER_REPLAY_SHOW = 'PUPPETEER_REPLAY_SHOW';
    public const PUPPETEER_REPLAY_DELETE = 'PUPPETEER_REPLAY_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::PUPPETEER_REPLAY_SHOW, self::PUPPETEER_REPLAY_DELETE]) && $subject instanceof PuppeteerReplay;
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

        $subjectPupeteerReplay = $subject;

        // Check For Other
        return match ($attribute) {
            self::PUPPETEER_REPLAY_SHOW => $this->canShow($subjectPupeteerReplay, $user),
            self::PUPPETEER_REPLAY_DELETE => $this->canDelete($subjectPupeteerReplay, $user),
            default => FALSE
        };

    }

    private function canShow(PuppeteerReplay $puppeteerReplay, User|UserInterface $loggedUser): bool
    {
        if ($loggedUser->getUserIdentifier() == $puppeteerReplay->getCreatedBy()->getUserIdentifier()) {
            return TRUE;
        }
        return FALSE;
    }

    private function canDelete(PuppeteerReplay $puppeteerReplay, User|UserInterface $loggedUser): bool
    {
        if ($loggedUser->getUserIdentifier() == $puppeteerReplay->getCreatedBy()->getUserIdentifier()) {
            return TRUE;
        }
        return FALSE;
    }

}
