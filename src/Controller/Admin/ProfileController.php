<?php namespace App\Controller\Admin;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Translation\LocaleSwitcher;
use Twig\Environment;

#[Route(path: '/admin/profile', name: 'app_admin_profile_')]
class ProfileController extends AbstractController
{
    #[Route('/switch_to_light_mode', name: 'switch_to_light_mode')]
    public function switchToLightMode(#[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {
        $loggedUser->setDarkMode(FALSE);
        $entityManager->persist($loggedUser);
        $entityManager->flush();
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

    #[Route('/switch_to_dark_mode', name: 'switch_to_dark_mode')]
    public function switchToDarkMode(#[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {
        $loggedUser->setDarkMode(TRUE);
        $entityManager->persist($loggedUser);
        $entityManager->flush();
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

    #[Route('/switch_locale_to/{fallback}', name: 'switch_locale_to')]
    public function switchLocaleTo(#[CurrentUser] User $loggedUser, ?string $fallback, EntityManagerInterface $entityManager, Environment $twig): Response
    {
        $validFallbacks = $twig->getGlobals()["layout"]["locales"];
        foreach ($validFallbacks as $validFallback) {
            if ($validFallback["fallback"] === $fallback) {
                $loggedUser->setLocale($fallback);
                $entityManager->persist($loggedUser);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

}