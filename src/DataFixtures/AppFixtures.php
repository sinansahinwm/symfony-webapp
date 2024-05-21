<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\CityFactory;
use App\Factory\CountryFactory;
use App\Factory\DistrictFactory;
use App\Factory\HelpCenterArticleFactory;
use App\Factory\HelpCenterCategoryFactory;
use App\Factory\NotificationFactory;
use App\Factory\SubscriptionPlanFactory;
use App\Factory\TeamFactory;
use App\Factory\TeamInviteFactory;
use App\Factory\UserActivityFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class AppFixtures extends Fixture
{
    const USERS_COUNT = 100;
    const TEAMS_COUNT = 5;
    const TEAM_INVITES_COUNT = 3;
    const COLLABORATORS_PER_TEAM = 5;
    const NOTIFICATIONS_COUNT_PER_USER = 10;
    const USER_ACTIVITIES_COUNT = 300;

    const SUBSCRIPTION_PLANS_COUNT = 3;
    const PLAN_FEATURES_PER_PLAN = 4;

    const HELP_CENTER_CATEGORIES_COUNT = 5;
    const HELP_CENTER_ARTICLES_COUNT = 50;

    const ALLOWED_COUNTRIES = ["Turkey"];
    const DATETIME_SEED_BETWEEEN = '-100 days';
    const ADMINISTRATOR_ROLES = ["ROLE_USER", "ROLE_ADMIN", "ROLE_CAN_SWITCH"];

    // Tweak Configuration
    const DISABLE_HASHING_PASSWORDS_WHEN_LOADING_FIXTURES = TRUE;

    public function __construct(private ResetPasswordHelperInterface $resetPasswordHelper, private UserPasswordHasherInterface $userPasswordHasher, private EntityManagerInterface $entityManager, private ContainerBagInterface $containerBag)
    {
    }

    public function load(ObjectManager $manager): void
    {

        // Create Administrator
        $this->createAdministrator();

        // return;

        // Set Ini For Fixtures
        ini_set('memory_limit', '-1');

        // Create Countries & Cities & Districts
        self::createCountriesAndCities();

        // Create Users
        $myUsers = UserFactory::createMany(self::USERS_COUNT);

        // Create Teams
        $myTeams = TeamFactory::createMany(self::TEAMS_COUNT);

        // Create User Password Requests
        $this->createUserPasswordRequests();

        // Create TeamInvites
        TeamInviteFactory::createMany(self::TEAM_INVITES_COUNT);

        // Create Notifications
        $myNotifications = NotificationFactory::createMany(self::NOTIFICATIONS_COUNT_PER_USER);

        // Create User Activities
        $myUserActivities = UserActivityFactory::createMany(self::USER_ACTIVITIES_COUNT);

        // Create Subscription Plans
        $mySubscriptionPlans = SubscriptionPlanFactory::createMany(self::SUBSCRIPTION_PLANS_COUNT);

        // Create Help Center Categories
        $helpCenterCategories = HelpCenterCategoryFactory::createMany(self::HELP_CENTER_CATEGORIES_COUNT);

        // Create Help Center Articles
        $helpCenterArticles = HelpCenterArticleFactory::createMany(self::HELP_CENTER_ARTICLES_COUNT);

    }

    private function createAdministrator(): void
    {

        $adminEmail = $this->containerBag->get("app.fixtures.administrator.email");
        $adminPlainPassword = $this->containerBag->get("app.fixtures.administrator.password");

        $myUser = new User();
        $myUser->setEmail($adminEmail);
        $myUser->setPassword($this->userPasswordHasher->hashPassword($myUser, $adminPlainPassword));
        $myUser->setRoles(self::ADMINISTRATOR_ROLES);
        $myUser->setDisplayName("Administrator");
        $myUser->setPhone(NULL);
        $myUser->setIsVerified(TRUE);
        $myUser->setIsPassive(FALSE);
        $myUser->setLocale(NULL);
        $myUser->setDarkMode(FALSE);
        $myUser->setTeam(NULL);
        $this->entityManager->persist($myUser);
        $this->entityManager->flush();
    }

    private function createUserPasswordRequests(): void
    {
        $randomUsers = UserFactory::randomSet(ceil(self::USERS_COUNT / 5));
        foreach ($randomUsers as $randomUser) {
            try {
                $this->resetPasswordHelper->generateResetToken($randomUser);
            } catch (Exception|ResetPasswordExceptionInterface $exception) {

            }
        }
    }

    private static function createCountriesAndCities(): void
    {
        $sourceUrl = 'https://github.com/dr5hn/countries-states-cities-database/raw/master/yml/countries+states+cities.yml';
        $sourceContent = file_get_contents($sourceUrl);
        $parsedSource = Yaml::parse($sourceContent, Yaml::PARSE_OBJECT);
        $sourceFinalData = $parsedSource["country_state_city"];

        foreach ($sourceFinalData as $parsedCountry) {

            if ((in_array($parsedCountry["name"], self::ALLOWED_COUNTRIES) === FALSE) && count(self::ALLOWED_COUNTRIES) > 0) {
                continue;
            }

            // Create Countries
            $myCountry = CountryFactory::createOne([
                'iso2' => $parsedCountry["iso2"],
                'iso3' => $parsedCountry["iso3"],
                'name' => $parsedCountry["name"],
                'native' => $parsedCountry["native"] ?? $parsedCountry["name"],
                'latitude' => $parsedCountry["latitude"] ?? 0,
                'longitude' => $parsedCountry["longitude"] ?? 0,
                'emoji' => base64_encode($parsedCountry["emoji"]),
                'phone_code' => $parsedCountry["phone_code"],
                'currency_name' => $parsedCountry["currency_name"],
                'currency_symbol' => $parsedCountry["currency_symbol"],
            ]);

            $parsedCities = $parsedCountry["states"];
            foreach ($parsedCities as $parsedCity) {

                // Create Cities
                $myCity = CityFactory::createOne([
                    "name" => $parsedCity["name"],
                    "latitude" => $parsedCity["latitude"] ?? 0,
                    "longitude" => $parsedCity["longitude"] ?? 0,
                    "country" => $myCountry->object()
                ]);

                // Create Districts
                $parsedDistricts = $parsedCity["cities"];

                foreach ($parsedDistricts as $parsedDistrict) {
                    DistrictFactory::createOne([
                        "name" => $parsedDistrict["name"],
                        "latitude" => $parsedDistrict["latitude"] ?? 0,
                        "longitude" => $parsedDistrict["longitude"] ?? 0,
                        "city" => $myCity->object()
                    ]);
                }
            }
        }

    }
}
