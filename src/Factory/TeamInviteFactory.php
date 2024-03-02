<?php

namespace App\Factory;

use App\DataFixtures\AppFixtures;
use App\Entity\TeamInvite;
use App\Repository\TeamInviteRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TeamInvite>
 *
 * @method        TeamInvite|Proxy create(array|callable $attributes = [])
 * @method static TeamInvite|Proxy createOne(array $attributes = [])
 * @method static TeamInvite|Proxy find(object|array|mixed $criteria)
 * @method static TeamInvite|Proxy findOrCreate(array $attributes)
 * @method static TeamInvite|Proxy first(string $sortedField = 'id')
 * @method static TeamInvite|Proxy last(string $sortedField = 'id')
 * @method static TeamInvite|Proxy random(array $attributes = [])
 * @method static TeamInvite|Proxy randomOrCreate(array $attributes = [])
 * @method static TeamInviteRepository|RepositoryProxy repository()
 * @method static TeamInvite[]|Proxy[] all()
 * @method static TeamInvite[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TeamInvite[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static TeamInvite[]|Proxy[] findBy(array $attributes)
 * @method static TeamInvite[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TeamInvite[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class TeamInviteFactory extends ModelFactory
{
    public const TEAM_INVITE_TYPE_BY_USER = 0;
    public const TEAM_INVITE_TYPE_BY_EMAIL = 1;

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $inviteType = [self::TEAM_INVITE_TYPE_BY_USER, self::TEAM_INVITE_TYPE_BY_EMAIL];
        $randomInviteType = $inviteType[array_rand($inviteType)];

        return [
            'team' => TeamFactory::random(),
            'user' => ($randomInviteType === self::TEAM_INVITE_TYPE_BY_USER) ? UserFactory::createOne() : NULL,
            'created_at' => self::faker()->dateTimeBetween(AppFixtures::DATETIME_SEED_BETWEEEN),
            'email_address' => ($randomInviteType === self::TEAM_INVITE_TYPE_BY_EMAIL) ? self::faker()->freeEmail() : NULL
        ];
    }

    protected function initialize(): self
    {
        return $this// ->afterInstantiate(function(TeamInvite $teamInvite): void {})
            ;
    }

    protected static function getClass(): string
    {
        return TeamInvite::class;
    }
}
