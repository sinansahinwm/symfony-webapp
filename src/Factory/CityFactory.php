<?php

namespace App\Factory;

use App\Entity\City;
use App\Repository\CityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<City>
 *
 * @method        City|Proxy create(array|callable $attributes = [])
 * @method static City|Proxy createOne(array $attributes = [])
 * @method static City|Proxy find(object|array|mixed $criteria)
 * @method static City|Proxy findOrCreate(array $attributes)
 * @method static City|Proxy first(string $sortedField = 'id')
 * @method static City|Proxy last(string $sortedField = 'id')
 * @method static City|Proxy random(array $attributes = [])
 * @method static City|Proxy randomOrCreate(array $attributes = [])
 * @method static CityRepository|RepositoryProxy repository()
 * @method static City[]|Proxy[] all()
 * @method static City[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static City[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static City[]|Proxy[] findBy(array $attributes)
 * @method static City[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static City[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class CityFactory extends ModelFactory
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'latitude' => self::faker()->text(50),
            'longitude' => self::faker()->text(50),
            'name' => self::faker()->text(255),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(City $city): void {})
        ;
    }

    protected static function getClass(): string
    {
        return City::class;
    }
}
