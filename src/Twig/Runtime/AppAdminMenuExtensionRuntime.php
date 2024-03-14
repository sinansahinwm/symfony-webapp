<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Dto\UserMenuDto;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppAdminMenuExtensionRuntime implements RuntimeExtensionInterface
{

    const DEFAULT_AVATAR_LETTER = '...';

    public function __construct(private TranslatorInterface $translator)
    {

    }

    public function appAdminMenu(User $user): iterable
    {
        $menuItems = [
            MenuItem::section($this->t('ÖZET')),
            MenuItem::linkToRoute('Genel Bakış', 'bx bx-home-alt', 'app_admin_dashboard'),
            MenuItem::subMenu('Profilim', 'bx bx-user')->setBadge("DE")->setSubItems([
                MenuItem::linkToRoute('Profilim', 'bx bx-home-circle', 'app_admin_dashboard')->setBadge("1"),
                MenuItem::linkToRoute('Bildirimler', 'bx bx-home-circle', 'app_admin_notification_index'),
                MenuItem::linkToRoute('Takım Panosu', 'bx bx-home-circle', 'app_admin_dashboard'),
            ]),
            MenuItem::section($this->t('Ayarlar')),
        ];
        return $this->convertMenuItemsToDto($menuItems);
    }

    public function appUserMenu(User $user): UserMenuDto
    {
        $menuItems = $this->convertMenuItemsToDto([
            MenuItem::section(),
            MenuItem::linkToRoute('Profilim', 'bx bx-user', 'app_admin_profile_current'),
            MenuItem::linkToRoute('Dokümantasyon', 'bx bx-help-circle', 'app_admin_dashboard'),
            MenuItem::section(),
            MenuItem::linkToExitImpersonation("Taklit Modundan Çık", 'bx bx-bomb'),
            MenuItem::linkToLogout($this->t('Güvenli Çıkış'), 'bx bx-power-off'),
        ]);

        return UserMenu::new()
            ->setMenuItems($menuItems)
            ->setName($user->getDisplayName() ?? $user->getEmail())
            ->setGravatarEmail($user->getEmail())
            ->setAvatarUrl($this->createUserAvatarUrl($user))
            ->getAsDto();
    }

    private function convertMenuItemsToDto(iterable $menuItems): iterable
    {
        $dtoItems = [];
        foreach ($menuItems as $menuItem) {
            $dtoItems[] = $menuItem->getAsDto();
        }
        return $dtoItems;
    }

    private function t(string $label): string
    {
        return $this->translator->trans($label);
    }

    private function createUserAvatarUrl(User $user): string
    {
        $urlParts = ['https://placehold.co/', '400', 'jpg', '?text=' . self::getAvatarCharsFromString($user->getDisplayName())];
        return implode('', $urlParts);
    }

    private static function getAvatarCharsFromString(?string $rawString): string
    {
        if ($rawString === NULL) {
            return self::DEFAULT_AVATAR_LETTER;
        } else {
            $rawCamelTitleAW = (new UnicodeString($rawString))->camel()->title(TRUE)->toString();
            $upperChars = preg_replace('![^A-Z]+!', '', $rawCamelTitleAW);
            if (strlen($upperChars) >= 2) {
                return mb_substr($upperChars, 0, 2);
            }
        }
        return self::DEFAULT_AVATAR_LETTER;
    }

}
