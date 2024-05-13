<?php

namespace App\Security\Voter;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TeamVoter extends Voter
{

    public const TEAM_EDIT = 'TEAM_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::TEAM_EDIT]) && $subject instanceof Team;
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

        $subjectTeam = $subject;

        // Check For Other
        return match ($attribute) {
            self::TEAM_EDIT => $this->canEditTeam($subjectTeam, $user),
            default => FALSE
        };

    }

    private function canEditTeam(Team $subjectTeam, User|UserInterface $loggedUser): bool
    {
        $teamOwnerID = $subjectTeam->getOwnerId();
        if ($loggedUser->getId() == $teamOwnerID) {
            return TRUE;
        }
        return FALSE;
    }

}
