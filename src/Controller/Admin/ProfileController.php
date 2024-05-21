<?php namespace App\Controller\Admin;

use App\Config\UserActivityType;
use App\Entity\User;
use App\Entity\UserPreferences;
use App\Form\Auth\Profile\ProfileChangePasswordType;
use App\Form\Auth\Profile\ProfileEditType;
use App\Form\Auth\Profile\ProfileKickTeamType;
use App\Form\Auth\Profile\ProfileMakePassiveType;
use App\Form\UserPreferencesType;
use App\Security\LoginFormAuthenticator;
use App\Service\UserActivityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\LocaleSwitcher;
use Twig\Environment;
use function Symfony\Component\Translation\t;

#[Route(path: '/admin/profile', name: 'app_admin_profile_')]
class ProfileController extends AbstractController
{

    #[Route(path: '/', name: 'current')]
    public function loggedUserProfile(#[CurrentUser] User $loggedUser): Response
    {
        return $this->redirectToRoute('app_admin_profile_show', ['theUser' => $loggedUser->getId()]);
    }

    #[IsGranted('PROFILE_SHOW', 'theUser')]
    #[Route(path: '/{theUser}', name: 'show')]
    public function otherUserProfile(User $theUser): Response
    {
        return $this->render('admin/profile/index.html.twig', ["user" => $theUser]);
    }

    #[IsGranted('PROFILE_EDIT', 'theUser')]
    #[Route(path: '/{theUser}/edit', name: 'edit')]
    public function userEdit(User $theUser, Request $request, EntityManagerInterface $entityManager): Response
    {

        $myForm = $this->createForm(ProfileEditType::class, $theUser, [
            'readonlyValue' => $theUser->getEmail(),
        ]);

        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {
            $entityManager->persist($theUser);
            $entityManager->flush();
            $this->addFlash('pageNotificationSuccess', t("Profil bilgileriniz başarıyla kaydedildi."));
        }

        return $this->render('admin/profile/edit.html.twig', ["user" => $theUser, "form" => $myForm]);
    }

    #[IsGranted('PROFILE_EDIT', 'theUser')]
    #[Route(path: '/{theUser}/preferences', name: 'preferences')]
    public function userPreferences(User $theUser, Request $request, EntityManagerInterface $entityManager): Response
    {
        $myUserPreferences = $theUser->getPreferences();
        $myForm = $this->createForm(UserPreferencesType::class, $myUserPreferences);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {
            $entityManager->persist($myUserPreferences);
            $entityManager->flush();
            $this->addFlash('pageNotificationSuccess', t("Profil tercihleriniz başarıyla kaydedildi."));
        }

        return $this->render('admin/profile/preferences.html.twig', ["user" => $theUser, "theForm" => $myForm]);
    }

    #[IsGranted('PROFILE_EDIT', 'theUser')]
    #[Route(path: '/{theUser}/change_theme/{_theme}', name: 'change_theme', requirements: ["_theme" => "default|raspberry|bordered|semidark"], defaults: ["_theme" => "default"])]
    public function userChangeTheme(User $theUser, EntityManagerInterface $entityManager, string $_theme): Response
    {
        $theUser->setPreferredTheme($_theme);
        $theUser->setDarkMode(FALSE);
        $entityManager->persist($theUser);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[IsGranted('PROFILE_CHANGE_PASSWORD', 'theUser')]
    #[Route(path: '/{theUser}/change_password', name: 'change_password')]
    public function userChangePassword(User $theUser, Request $request, UserActivityService $userActivityService, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $myForm = $this->createForm(ProfileChangePasswordType::class, $theUser);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {

            $passwordIsValid = $userPasswordHasher->isPasswordValid($theUser, $myForm->get("oldPassword")->getData());
            if ($passwordIsValid) {
                $hashedNewPassword = $userPasswordHasher->hashPassword($theUser, $myForm->get("newPassword")->getData());
                $theUser->setPassword($hashedNewPassword);
                $entityManager->persist($theUser);
                $entityManager->flush();
                $this->addFlash('pageNotificationSuccess', t("Şifreniz başarıyla değiştirildi."));

                // Release User Activity
                $userActivityService->releaseActivity($theUser, UserActivityType::USER_PASSWORD_CHANGED);

            } else {
                $this->addFlash('pageNotificationError', t("Eski şifrenizi yanlış girdiniz."));
            }
        }

        return $this->render('admin/profile/change_password.html.twig', ["user" => $theUser, "form" => $myForm]);
    }

    #[IsGranted('PROFILE_MAKE_PASSIVE', 'theUser')]
    #[Route(path: '/{theUser}/make_passive', name: 'make_passive')]
    public function userMakePassive(User $theUser, Request $request, EntityManagerInterface $entityManager, #[CurrentUser] User $loggedUser): Response
    {
        $myForm = $this->createForm(ProfileMakePassiveType::class, $theUser);
        $myForm->handleRequest($request);

        $usersTeam = $theUser->getTeam();
        if ($myForm->isSubmitted() && $myForm->isValid()) {
            if (in_array("ROLE_ADMIN", $loggedUser->getRoles()) || ($usersTeam !== NULL && $usersTeam->getOwnerId() === $loggedUser->getId() && $theUser->getId() !== $loggedUser->getId())) {
                if ($usersTeam->getOwnerId() !== $theUser->getId()) {
                    $theUser->setIsPassive(TRUE);
                    $entityManager->persist($theUser);
                    $entityManager->flush();
                    $this->addFlash('pageNotificationSuccess', t("Kullanıcı hesabı pasife alındı."));
                } else {
                    $this->addFlash('pageNotificationError', t("Takım kurucusu hesabı pasife alınamaz."));
                }
            } else {
                $this->addFlash('pageNotificationError', t("Kullanıcı hesabı pasife alınamadı. Takımınızda olmayan bir kullanıcıyı pasife alamazsınız."));
            }
        }

        return $this->render('admin/profile/make_passive.html.twig', ["user" => $theUser, "form" => $myForm]);
    }

    #[IsGranted('PROFILE_MAKE_PASSIVE', 'theUser')]
    #[Route(path: '/{theUser}/make_active', name: 'make_active')]
    public function userMakeActive(User $theUser, EntityManagerInterface $entityManager): Response
    {
        $theUser->setIsPassive(FALSE);
        $entityManager->persist($theUser);
        $entityManager->flush();
        $this->addFlash('pageNotificationSuccess', t("Kullanıcı hesabı aktive edildi."));
        return $this->redirectToRoute('app_admin_profile_edit', ["theUser" => $theUser->getId()]);
    }

    #[IsGranted('PROFILE_KICK_TEAM', 'theUser')]
    #[Route(path: '/{theUser}/kick_team', name: 'kick_team')]
    public function userKickTeam(User $theUser, Request $request, EntityManagerInterface $entityManager, #[CurrentUser] User $loggedUser): Response
    {
        $myForm = $this->createForm(ProfileKickTeamType::class, $theUser);
        $myForm->handleRequest($request);

        if ($myForm->isSubmitted() && $myForm->isValid()) {
            $usersTeam = $theUser->getTeam();
            if ($usersTeam !== NULL && $usersTeam->getOwnerId() === $loggedUser->getId() && $theUser->getId() !== $loggedUser->getId()) {
                $theUser->setTeam(NULL);
                $entityManager->persist($theUser);
                $entityManager->flush();
                $this->addFlash('pageNotificationSuccess', t("Kullanıcı takımdan çıkarıldı."));
            } else {
                $this->addFlash('pageNotificationError', t("Kullanıcı takımdan çıkarılamadı. Kurucusu olduğunuz takımdan çıkamazsınız."));
            }
        }

        return $this->render('admin/profile/kick_team.html.twig', ["user" => $theUser, "form" => $myForm]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route(path: '/{theUser}/impersonate', name: 'impersonate')]
    public function userImpersonate(User $theUser, UrlGeneratorInterface $urlGenerator): Response
    {
        $dashboardURL = $urlGenerator->generate('app_admin_dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $impersonatePath = $dashboardURL . "?_impersonate_user=" . $theUser->getUserIdentifier();
        return $this->redirect($impersonatePath);
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