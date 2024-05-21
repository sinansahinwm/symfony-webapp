<?php

namespace App\Factory;

use App\Entity\HelpCenterCategory;
use App\Repository\HelpCenterCategoryRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<HelpCenterCategory>
 *
 * @method        HelpCenterCategory|Proxy                     create(array|callable $attributes = [])
 * @method static HelpCenterCategory|Proxy                     createOne(array $attributes = [])
 * @method static HelpCenterCategory|Proxy                     find(object|array|mixed $criteria)
 * @method static HelpCenterCategory|Proxy                     findOrCreate(array $attributes)
 * @method static HelpCenterCategory|Proxy                     first(string $sortedField = 'id')
 * @method static HelpCenterCategory|Proxy                     last(string $sortedField = 'id')
 * @method static HelpCenterCategory|Proxy                     random(array $attributes = [])
 * @method static HelpCenterCategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static HelpCenterCategoryRepository|RepositoryProxy repository()
 * @method static HelpCenterCategory[]|Proxy[]                 all()
 * @method static HelpCenterCategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static HelpCenterCategory[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static HelpCenterCategory[]|Proxy[]                 findBy(array $attributes)
 * @method static HelpCenterCategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static HelpCenterCategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class HelpCenterCategoryFactory extends ModelFactory
{

    public function __construct(private SluggerInterface $slugger)
    {
        parent::__construct();
    }


    protected function getDefaults(): array
    {
        $categoryName = self::faker()->name;
        $sluggedName = $this->slugger->slug($categoryName);

        return [
            'category_id' => $sluggedName,
            'icon' => 'bx bx-category',
            'name' => $categoryName
        ];
    }


    protected function initialize(): self
    {
        return $this// ->afterInstantiate(function(HelpCenterCategory $helpCenterCategory): void {})
            ;
    }

    protected static function getClass(): string
    {
        return HelpCenterCategory::class;
    }
}
