<?php

namespace App\Factory;

use App\Config\NotificationPriorityType;
use App\DataFixtures\AppFixtures;
use App\Entity\Notification;
use App\Repository\NotificationRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Notification>
 *
 * @method        Notification|Proxy create(array|callable $attributes = [])
 * @method static Notification|Proxy createOne(array $attributes = [])
 * @method static Notification|Proxy find(object|array|mixed $criteria)
 * @method static Notification|Proxy findOrCreate(array $attributes)
 * @method static Notification|Proxy first(string $sortedField = 'id')
 * @method static Notification|Proxy last(string $sortedField = 'id')
 * @method static Notification|Proxy random(array $attributes = [])
 * @method static Notification|Proxy randomOrCreate(array $attributes = [])
 * @method static NotificationRepository|RepositoryProxy repository()
 * @method static Notification[]|Proxy[] all()
 * @method static Notification[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Notification[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Notification[]|Proxy[] findBy(array $attributes)
 * @method static Notification[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Notification[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class NotificationFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $allPriorities = NotificationPriorityType::getValuesArray();
        $randomPriority = $allPriorities[array_rand($allPriorities)];
        return [
            'content' => self::faker()->words(5, TRUE),
            'created_at' => self::faker()->dateTimeBetween(AppFixtures::DATETIME_SEED_BETWEEEN),
            'priority' => $randomPriority,
            'to_user' => UserFactory::random(),
            'is_read' => self::faker()->boolean(),
            'url' => self::faker()->url()
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Notification $notification): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Notification::class;
    }
}
