<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileVoter extends Voter
{
    public const PROFILE_SHOW = 'PROFILE_SHOW';
    public const PROFILE_EDIT = 'PROFILE_EDIT';
    public const PROFILE_CHANGE_PASSWORD = 'PROFILE_CHANGE_PASSWORD';
    public const PROFILE_MAKE_PASSIVE = 'PROFILE_MAKE_PASSIVE';
    public const PROFILE_KICK_TEAM = 'PROFILE_KICK_TEAM';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::PROFILE_SHOW, self::PROFILE_EDIT, self::PROFILE_CHANGE_PASSWORD, self::PROFILE_MAKE_PASSIVE, self::PROFILE_KICK_TEAM]) && $subject instanceof User;
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

        $subjectUser = $subject;

        // Check For Other
        return match ($attribute) {
            self::PROFILE_SHOW => $this->canShow($subjectUser, $user),
            self::PROFILE_EDIT => $this->canEdit($subjectUser, $user),
            self::PROFILE_CHANGE_PASSWORD => $this->canChangePassword($subjectUser, $user),
            self::PROFILE_MAKE_PASSIVE => $this->canMakePassive($subjectUser, $user),
            self::PROFILE_KICK_TEAM => $this->canKickTeam($subjectUser, $user),
            default => FALSE
        };

    }

    private function canShow(User|UserInterface $subjectUser, User|UserInterface $loggedUser): bool
    {
        if ($subjectUser->getUserIdentifier() == $loggedUser->getUserIdentifier()) {
            return TRUE;
        }

        if ($subjectUser->getTeam() !== NULL && $loggedUser->getTeam() !== NULL) {
            if ($subjectUser->getTeam()->getId() !== NULL && $loggedUser->getTeam()->getId() !== NULL) {
                return TRUE;
            }
        }

        return FALSE;
    }

    private function canEdit(User|UserInterface $subjectUser, User|UserInterface $loggedUser): bool
    {
        if ($subjectUser->getUserIdentifier() == $loggedUser->getUserIdentifier()) {
            return TRUE;
        }

        return FALSE;
    }

    private function canChangePassword(User|UserInterface $subjectUser, User|UserInterface $loggedUser): bool
    {
        if ($subjectUser->getUserIdentifier() == $loggedUser->getUserIdentifier()) {
            return TRUE;
        }

        return FALSE;
    }

    private function canMakePassive(User|UserInterface $subjectUser, User|UserInterface $loggedUser): bool
    {
        if ($subjectUser->getTeam() !== NULL && $loggedUser->getTeam() !== NULL) {
            if ($subjectUser->getTeam()->getId() !== NULL && $loggedUser->getTeam()->getId() !== NULL) {
                $subjectTeamOwnerID = $subjectUser->getTeam()->getOwnerId();
                if ($subjectTeamOwnerID === $loggedUser->getId()) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    private function canKickTeam(User|UserInterface $subjectUser, User|UserInterface $loggedUser): bool
    {
        if ($subjectUser->getTeam() !== NULL && $loggedUser->getTeam() !== NULL) {
            if ($subjectUser->getTeam()->getId() !== NULL && $loggedUser->getTeam()->getId() !== NULL) {
                $subjectTeamOwnerID = $subjectUser->getTeam()->getOwnerId();
                if ($subjectTeamOwnerID === $loggedUser->getId()) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

}
