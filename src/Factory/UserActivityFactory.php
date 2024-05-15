<?php

namespace App\Factory;

use App\Config\UserActivityType;
use App\Entity\UserActivity;
use App\Repository\UserActivityRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<UserActivity>
 *
 * @method        UserActivity|Proxy                     create(array|callable $attributes = [])
 * @method static UserActivity|Proxy                     createOne(array $attributes = [])
 * @method static UserActivity|Proxy                     find(object|array|mixed $criteria)
 * @method static UserActivity|Proxy                     findOrCreate(array $attributes)
 * @method static UserActivity|Proxy                     first(string $sortedField = 'id')
 * @method static UserActivity|Proxy                     last(string $sortedField = 'id')
 * @method static UserActivity|Proxy                     random(array $attributes = [])
 * @method static UserActivity|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UserActivityRepository|RepositoryProxy repository()
 * @method static UserActivity[]|Proxy[]                 all()
 * @method static UserActivity[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static UserActivity[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static UserActivity[]|Proxy[]                 findBy(array $attributes)
 * @method static UserActivity[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static UserActivity[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UserActivityFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function getDefaults(): array
    {
        $activityTypes = [
            UserActivityType::LOGIN,
            UserActivityType::CREATE_TEAM,
            UserActivityType::RECEIVE_TEAM_INVITE,
        ];

        $randomActivityType = $activityTypes[array_rand($activityTypes)];

        return [
            'user' => UserFactory::random(),
            'activity_type' => $randomActivityType,
            'created_at' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];

    }

    protected function initialize(): self
    {
        return $this// ->afterInstantiate(function(UserActivity $userActivity): void {})
            ;
    }

    protected static function getClass(): string
    {
        return UserActivity::class;
    }
}
