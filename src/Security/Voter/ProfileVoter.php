<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileVoter extends Voter
{
    public const PROFILE_READ = 'PROFILE_READ';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::PROFILE_READ]) && $subject instanceof User;
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

        // Check For Subject Is Logged User
        if ($user->getUserIdentifier() === $subject->getUserIdentifier()) {
            return TRUE;
        }

        // Check For Team Collaborators
        $usersTeam = $user->getTeam();
        $subjectsTeam = $subject->getTeam();
        if ($usersTeam !== NULL && $subjectsTeam !== NULL) {
            if ($usersTeam->getId() === $subjectsTeam->getId()) {
                return TRUE;
            }
        }

        return false;
    }
}
