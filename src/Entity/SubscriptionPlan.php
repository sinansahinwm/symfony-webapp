<?php

namespace App\Entity;

use App\Repository\SubscriptionPlanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionPlanRepository::class)]
class SubscriptionPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $key_name = null;

    #[ORM\Column(length: 5)]
    private ?string $currency = null;

    #[ORM\Column]
    private ?int $payment_interval = null;

    #[ORM\Column]
    private ?int $trial_period_days = null;

    #[ORM\Column(length: 20)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(nullable: true)]
    private ?int $discount_percent = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_popular = null;

    #[ORM\Column(length: 10)]
    private ?string $currency_sign = null;

    #[ORM\Column]
    private ?int $plan_order = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $included_features = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $not_included_features = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyName(): ?string
    {
        return $this->key_name;
    }

    public function setKeyName(string $key_name): static
    {
        $this->key_name = $key_name;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPaymentInterval(): ?int
    {
        return $this->payment_interval;
    }

    public function setPaymentInterval(int $payment_interval): static
    {
        $this->payment_interval = $payment_interval;

        return $this;
    }

    public function getTrialPeriodDays(): ?int
    {
        return $this->trial_period_days;
    }

    public function setTrialPeriodDays(int $trial_period_days): static
    {
        $this->trial_period_days = $trial_period_days;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discount_percent;
    }

    public function setDiscountPercent(?int $discount_percent): static
    {
        $this->discount_percent = $discount_percent;

        return $this;
    }

    public function isIsPopular(): ?bool
    {
        return $this->is_popular;
    }

    public function setIsPopular(?bool $is_popular): static
    {
        $this->is_popular = $is_popular;

        return $this;
    }


    public function getCurrencySign(): ?string
    {
        return $this->currency_sign;
    }

    public function setCurrencySign(string $currency_sign): static
    {
        $this->currency_sign = $currency_sign;

        return $this;
    }

    public function getPlanOrder(): ?int
    {
        return $this->plan_order;
    }

    public function setPlanOrder(int $plan_order): static
    {
        $this->plan_order = $plan_order;

        return $this;
    }

    public function getIncludedFeatures(): ?string
    {
        return $this->included_features;
    }

    public function setIncludedFeatures(string $included_features): static
    {
        $this->included_features = $included_features;

        return $this;
    }

    public function getNotIncludedFeatures(): ?string
    {
        return $this->not_included_features;
    }

    public function setNotIncludedFeatures(?string $not_included_features): static
    {
        $this->not_included_features = $not_included_features;

        return $this;
    }

}
