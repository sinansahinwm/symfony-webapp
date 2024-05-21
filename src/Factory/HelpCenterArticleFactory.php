<?php

namespace App\Factory;

use App\Entity\HelpCenterArticle;
use App\Entity\HelpCenterCategory;
use App\Repository\HelpCenterArticleRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<HelpCenterArticle>
 *
 * @method        HelpCenterArticle|Proxy                     create(array|callable $attributes = [])
 * @method static HelpCenterArticle|Proxy                     createOne(array $attributes = [])
 * @method static HelpCenterArticle|Proxy                     find(object|array|mixed $criteria)
 * @method static HelpCenterArticle|Proxy                     findOrCreate(array $attributes)
 * @method static HelpCenterArticle|Proxy                     first(string $sortedField = 'id')
 * @method static HelpCenterArticle|Proxy                     last(string $sortedField = 'id')
 * @method static HelpCenterArticle|Proxy                     random(array $attributes = [])
 * @method static HelpCenterArticle|Proxy                     randomOrCreate(array $attributes = [])
 * @method static HelpCenterArticleRepository|RepositoryProxy repository()
 * @method static HelpCenterArticle[]|Proxy[]                 all()
 * @method static HelpCenterArticle[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static HelpCenterArticle[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static HelpCenterArticle[]|Proxy[]                 findBy(array $attributes)
 * @method static HelpCenterArticle[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static HelpCenterArticle[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class HelpCenterArticleFactory extends ModelFactory
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'article_category' => HelpCenterCategoryFactory::random(),
            'markdown_content' => self::faker()->text(),
            'title' => self::faker()->text(255),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(HelpCenterArticle $helpCenterArticle): void {})
        ;
    }

    protected static function getClass(): string
    {
        return HelpCenterArticle::class;
    }
}
