<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class PlanCheckoutType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    "placeholder" => t("örn; Ahmet GÜZEL"),
                ]
            ])
            ->add('card_number', TextType::class, [
                'attr' => [
                    "class" => "credit-card-mask",
                    "placeholder" => "örn; 1356 3215 6548 7898"
                ]
            ])
            ->add('exp_date', TextType::class, [
                'attr' => [
                    "class" => "expiry-date-mask",
                    "placeholder" => t("AA/YY")
                ]
            ])
            ->add('cvv', TextType::class, [
                'attr' => [
                    'class' => "cvv-code-mask",
                    "placeholder" => t("örn; 654"),
                    'maxlength' => "3"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
