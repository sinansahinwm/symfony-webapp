<?php namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Auth\Profile\ProfileChangePasswordType;
use App\Form\Auth\Profile\ProfileEditType;
use App\Form\Auth\Profile\ProfileKickTeamType;
use App\Form\Auth\Profile\ProfileMakePassiveType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Translation\LocaleSwitcher;
use Twig\Environment;

#[Route(path: '/admin/profile', name: 'app_admin_profile_')]
class ProfileController extends AbstractController
{

    #[Route(path: '/', name: 'current')]
    public function loggedUserProfile(#[CurrentUser] User $loggedUser): Response
    {
        return $this->redirectToRoute('app_admin_profile_show', ['theUser' => $loggedUser->getId()]);
    }

    #[Route(path: '/{theUser}', name: 'show')]
    public function otherUserProfile(User $theUser): Response
    {
        return $this->render('admin/profile/index.html.twig', ["user" => $theUser]);
    }

    #[Route(path: '/{theUser}/edit', name: 'edit')]
    public function userEdit(User $theUser, Request $request): Response
    {
        $myForm = $this->createForm(ProfileEditType::class, $theUser);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {
            exit("FORM VALID");
        }

        return $this->render('admin/profile/edit.html.twig', ["user" => $theUser]);
    }

    #[Route(path: '/{theUser}/change_password', name: 'change_password')]
    public function userChangePassword(User $theUser, Request $request): Response
    {
        $myForm = $this->createForm(ProfileChangePasswordType::class, $theUser);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {
            exit("FORM VALID");
        }

        return $this->render('admin/profile/change_password.html.twig', ["user" => $theUser]);
    }

    #[Route(path: '/{theUser}/make_passive', name: 'make_passive')]
    public function userMakePassive(User $theUser, Request $request): Response
    {
        $myForm = $this->createForm(ProfileMakePassiveType::class, $theUser);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {
            exit("FORM VALID");
        }

        return $this->render('admin/profile/make_passive.html.twig', ["user" => $theUser]);
    }

    #[Route(path: '/{theUser}/kick_team', name: 'kick_team')]
    public function userKickTeam(User $theUser, Request $request): Response
    {
        $myForm = $this->createForm(ProfileKickTeamType::class, $theUser);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {
            exit("FORM VALID");
        }

        return $this->render('admin/profile/kick_team.html.twig', ["user" => $theUser]);
    }

    #[Route('/current/switch_to_light_mode', name: 'switch_to_light_mode')]
    public function switchToLightMode(#[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {
        $loggedUser->setDarkMode(FALSE);
        $entityManager->persist($loggedUser);
        $entityManager->flush();
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

    #[Route('/current/switch_to_dark_mode', name: 'switch_to_dark_mode')]
    public function switchToDarkMode(#[CurrentUser] User $loggedUser, EntityManagerInterface $entityManager): Response
    {
        $loggedUser->setDarkMode(TRUE);
        $entityManager->persist($loggedUser);
        $entityManager->flush();
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

    #[Route('/current/switch_locale_to/{fallback}', name: 'switch_locale_to')]
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