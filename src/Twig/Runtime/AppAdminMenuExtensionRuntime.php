<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Dto\UserMenuDto;
use Symfony\Component\String\UnicodeString;
use Twig\Extension\RuntimeExtensionInterface;
use function Symfony\Component\Translation\t;

class AppAdminMenuExtensionRuntime implements RuntimeExtensionInterface
{

    const DEFAULT_AVATAR_LETTER = '...';

    public function appAdminMenu(User $user): iterable
    {
        $menuItems = [
            MenuItem::section(t('ÖZET')),
            MenuItem::linkToRoute(t('Genel Bakış'), 'bx bx-home-alt', 'app_admin_dashboard'),
            MenuItem::subMenu(t('Profilim'), 'bx bx-user')->setBadge("DE")->setSubItems([
                MenuItem::linkToRoute(t('Profilim'), 'bx bx-home-circle', 'app_admin_dashboard')->setBadge("1"),
                MenuItem::linkToRoute(t('Bildirimler'), 'bx bx-home-circle', 'app_admin_notification_index'),
                MenuItem::linkToRoute(t('Takım Panosu'), 'bx bx-home-circle', 'app_admin_dashboard'),
            ]),
            MenuItem::section(t('OTOMASYON')),
            MenuItem::subMenu(t('Servisler'), 'bx bx-import')->setSubItems([
                MenuItem::linkToRoute(t('Chrome Aktarıcısı'), 'bx bx-chrome', 'app_admin_puppeteer_replay_index'),
            ]),
            MenuItem::section(t('Ayarlar')),
        ];
        return $this->convertMenuItemsToDto($menuItems);
    }

    public function appUserMenu(User $user): UserMenuDto
    {
        $menuItems = $this->convertMenuItemsToDto([
            MenuItem::section(),
            MenuItem::linkToRoute(t('Profilim'), 'bx bx-user', 'app_admin_profile_current'),
            MenuItem::linkToRoute(t('Dokümantasyon'), 'bx bx-help-circle', 'app_admin_dashboard'),
            MenuItem::section(),
            MenuItem::linkToExitImpersonation(t("Taklit Modundan Çık"), 'bx bx-bomb'),
            MenuItem::linkToLogout(t('Güvenli Çıkış'), 'bx bx-power-off'),
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
