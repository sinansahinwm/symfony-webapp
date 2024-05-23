<?php

namespace App\Factory;

use App\Config\WebScrapingRequestCompletedHandleType;
use App\Config\WebScrapingRequestStatusType;
use App\DataFixtures\AppFixtures;
use App\Entity\WebScrapingRequest;
use App\Repository\WebScrapingRequestRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<WebScrapingRequest>
 *
 * @method        WebScrapingRequest|Proxy                     create(array|callable $attributes = [])
 * @method static WebScrapingRequest|Proxy                     createOne(array $attributes = [])
 * @method static WebScrapingRequest|Proxy                     find(object|array|mixed $criteria)
 * @method static WebScrapingRequest|Proxy                     findOrCreate(array $attributes)
 * @method static WebScrapingRequest|Proxy                     first(string $sortedField = 'id')
 * @method static WebScrapingRequest|Proxy                     last(string $sortedField = 'id')
 * @method static WebScrapingRequest|Proxy                     random(array $attributes = [])
 * @method static WebScrapingRequest|Proxy                     randomOrCreate(array $attributes = [])
 * @method static WebScrapingRequestRepository|RepositoryProxy repository()
 * @method static WebScrapingRequest[]|Proxy[]                 all()
 * @method static WebScrapingRequest[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static WebScrapingRequest[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static WebScrapingRequest[]|Proxy[]                 findBy(array $attributes)
 * @method static WebScrapingRequest[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static WebScrapingRequest[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class WebScrapingRequestFactory extends ModelFactory
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'created_at' => self::faker()->dateTimeBetween(AppFixtures::DATETIME_SEED_BETWEEEN),
            'navigate_url' => self::faker()->url(),
            'webhook_url' => self::faker()->url(),
            'status' => WebScrapingRequestStatusType::NEWLY_CREATED,
            'completed_handle' => WebScrapingRequestCompletedHandleType::HANDLE_NULL,
        ];
    }

    protected function initialize(): self
    {
        return $this// ->afterInstantiate(function(WebScrapingRequest $webScrapingRequest): void {})
            ;
    }

    protected static function getClass(): string
    {
        return WebScrapingRequest::class;
    }
}
