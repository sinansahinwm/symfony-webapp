<?php

namespace App\Factory;

use App\DataFixtures\AppFixtures;
use App\Entity\Team;
use App\Entity\TeamOwnership;
use App\Entity\User;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Team>
 *
 * @method        Team|Proxy create(array|callable $attributes = [])
 * @method static Team|Proxy createOne(array $attributes = [])
 * @method static Team|Proxy find(object|array|mixed $criteria)
 * @method static Team|Proxy findOrCreate(array $attributes)
 * @method static Team|Proxy first(string $sortedField = 'id')
 * @method static Team|Proxy last(string $sortedField = 'id')
 * @method static Team|Proxy random(array $attributes = [])
 * @method static Team|Proxy randomOrCreate(array $attributes = [])
 * @method static TeamRepository|RepositoryProxy repository()
 * @method static Team[]|Proxy[] all()
 * @method static Team[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Team[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Team[]|Proxy[] findBy(array $attributes)
 * @method static Team[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Team[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class TeamFactory extends ModelFactory
{

    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => mb_substr(self::faker()->company(), 0, 20),
            'created_at' => self::faker()->dateTimeBetween(AppFixtures::DATETIME_SEED_BETWEEEN),
            'users' => UserFactory::createMany(AppFixtures::COLLABORATORS_PER_TEAM)
        ];
    }

    protected function initialize(): self
    {
        return $this->afterInstantiate(function (Team $team): void {
            // Add Random Owner With Related Users
            $relatedUsers = $team->getUsers();
            $userIDArray = [];
            foreach ($relatedUsers as $relatedUser) {
                $userIDArray[] = $relatedUser->getId();
            }
            $randomOwnerID = $userIDArray[array_rand($userIDArray)];
            $theOwnership = (new TeamOwnership())->setUser($this->userRepository->find($randomOwnerID))->setTeam($team);
            $this->entityManager->persist($theOwnership);
        });
    }

    protected static function getClass(): string
    {
        return Team::class;
    }

}
