<?php

namespace App\Factory;

use App\Entity\Marketplace;
use App\Repository\MarketplaceRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Marketplace>
 *
 * @method        Marketplace|Proxy                     create(array|callable $attributes = [])
 * @method static Marketplace|Proxy                     createOne(array $attributes = [])
 * @method static Marketplace|Proxy                     find(object|array|mixed $criteria)
 * @method static Marketplace|Proxy                     findOrCreate(array $attributes)
 * @method static Marketplace|Proxy                     first(string $sortedField = 'id')
 * @method static Marketplace|Proxy                     last(string $sortedField = 'id')
 * @method static Marketplace|Proxy                     random(array $attributes = [])
 * @method static Marketplace|Proxy                     randomOrCreate(array $attributes = [])
 * @method static MarketplaceRepository|RepositoryProxy repository()
 * @method static Marketplace[]|Proxy[]                 all()
 * @method static Marketplace[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Marketplace[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Marketplace[]|Proxy[]                 findBy(array $attributes)
 * @method static Marketplace[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Marketplace[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class MarketplaceFactory extends ModelFactory
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'logo' => self::faker()->imageUrl(),
            'name' => self::faker()->text(10),
            'url' => self::faker()->url,
        ];
    }


    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Marketplace $marketplace): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Marketplace::class;
    }
}
