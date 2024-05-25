<?php

namespace App\Factory;

use App\DataFixtures\AppFixtures;
use App\Entity\SubscriptionPlan;
use App\Repository\SubscriptionPlanRepository;
use Symfony\Component\String\UnicodeString;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<SubscriptionPlan>
 *
 * @method        SubscriptionPlan|Proxy                     create(array|callable $attributes = [])
 * @method static SubscriptionPlan|Proxy                     createOne(array $attributes = [])
 * @method static SubscriptionPlan|Proxy                     find(object|array|mixed $criteria)
 * @method static SubscriptionPlan|Proxy                     findOrCreate(array $attributes)
 * @method static SubscriptionPlan|Proxy                     first(string $sortedField = 'id')
 * @method static SubscriptionPlan|Proxy                     last(string $sortedField = 'id')
 * @method static SubscriptionPlan|Proxy                     random(array $attributes = [])
 * @method static SubscriptionPlan|Proxy                     randomOrCreate(array $attributes = [])
 * @method static SubscriptionPlanRepository|RepositoryProxy repository()
 * @method static SubscriptionPlan[]|Proxy[]                 all()
 * @method static SubscriptionPlan[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static SubscriptionPlan[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static SubscriptionPlan[]|Proxy[]                 findBy(array $attributes)
 * @method static SubscriptionPlan[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static SubscriptionPlan[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class SubscriptionPlanFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $planName = self::faker()->words(2, TRUE);
        $planKeyName = (new UnicodeString($planName))->snake();

        $planFeatures = array_map(function ($myIndex) {
            return self::faker()->words(3, TRUE);
        }, range(1, AppFixtures::PLAN_FEATURES_PER_PLAN));

        $planFeaturesNotIncluded = array_map(function ($myIndex) {
            return self::faker()->words(3, TRUE);
        }, range(1, ceil(AppFixtures::PLAN_FEATURES_PER_PLAN / 2)));

        return [
            'currency' => self::faker()->currencyCode,
            'currency_sign' => '₺',
            'key_name' => $planKeyName,
            'name' => $planName,
            'payment_interval' => 30, //self::faker()->numberBetween(15, 60),
            'trial_period_days' => 14, // self::faker()->numberBetween(7, 15),
            'amount' => self::faker()->randomFloat(2, 500, 999),
            'discount_percent' => self::faker()->numberBetween(0, 100),
            'included_features' => implode(',', $planFeatures),
            'not_included_features' => implode(',', $planFeaturesNotIncluded),
            'is_popular' => self::faker()->boolean(),
            'plan_order' => self::faker()->numberBetween(0, AppFixtures::SUBSCRIPTION_PLANS_COUNT),
        ];
    }

    protected function initialize(): self
    {
        return $this// ->afterInstantiate(function(SubscriptionPlan $subscriptionPlan): void {})
            ;
    }

    protected static function getClass(): string
    {
        return SubscriptionPlan::class;
    }
}
