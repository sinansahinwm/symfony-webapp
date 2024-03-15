<?php

namespace App\Factory;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method        User|Proxy create(array|callable $attributes = [])
 * @method static User|Proxy createOne(array $attributes = [])
 * @method static User|Proxy find(object|array|mixed $criteria)
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy first(string $sortedField = 'id')
 * @method static User|Proxy last(string $sortedField = 'id')
 * @method static User|Proxy random(array $attributes = [])
 * @method static User|Proxy randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static User[]|Proxy[] all()
 * @method static User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[] findBy(array $attributes)
 * @method static User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $theEmail = self::faker()->companyEmail();
        $emailExist = UserFactory::findBy(["email" => $theEmail]);

        return [
            'email' => (count($emailExist) === 0) ? $theEmail : self::faker()->companyEmail(),
            'password' => self::faker()->password(12),
            'display_name' => mb_substr(self::faker()->name(), 0, 20),
            'phone' => self::faker()->e164PhoneNumber(),
            'created_at' => self::faker()->dateTimeBetween(AppFixtures::DATETIME_SEED_BETWEEEN),
            'is_verified' => self::faker()->boolean(),
            'dark_mode' => self::faker()->boolean(),
            'locale' => strtolower(self::faker()->languageCode()),
            'team' => NULL,
            'roles' => ["ROLE_USER"],
        ];
    }

    protected function initialize(): self
    {
        return $this->afterInstantiate(function (User $user): void {
            if (AppFixtures::DISABLE_HASHING_PASSWORDS_WHEN_LOADING_FIXTURES === TRUE) {
                $user->setPassword(self::faker()->randomLetter());
            } else {
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
            }
        });
    }

    protected static function getClass(): string
    {
        return User::class;
    }

}
