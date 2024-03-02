<?php

namespace App\Factory;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Country>
 *
 * @method        Country|Proxy create(array|callable $attributes = [])
 * @method static Country|Proxy createOne(array $attributes = [])
 * @method static Country|Proxy find(object|array|mixed $criteria)
 * @method static Country|Proxy findOrCreate(array $attributes)
 * @method static Country|Proxy first(string $sortedField = 'id')
 * @method static Country|Proxy last(string $sortedField = 'id')
 * @method static Country|Proxy random(array $attributes = [])
 * @method static Country|Proxy randomOrCreate(array $attributes = [])
 * @method static CountryRepository|RepositoryProxy repository()
 * @method static Country[]|Proxy[] all()
 * @method static Country[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Country[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Country[]|Proxy[] findBy(array $attributes)
 * @method static Country[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Country[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class CountryFactory extends ModelFactory
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'iso2' => self::faker()->text(10),
            'iso3' => self::faker()->text(10),
            'name' => self::faker()->text(255),
            'native' => self::faker()->text(255),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Country $country): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Country::class;
    }
}
