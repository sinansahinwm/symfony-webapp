<?php namespace App\Form\Administrator;

use App\Entity\SubscriptionPlan;
use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class SubscriptionPlanType extends AbstractFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('key_name', TextType::class, [
                'label' => t("Anahtar Ad")
            ])
            ->add('name', TextType::class, [
                'label' => t("Plan Adı")
            ])
            ->add('currency', TextType::class, [
                'label' => t("Kur")
            ])
            ->add('currency_sign', TextType::class, [
                'label' => "Kur Simgesi"
            ])
            ->add('payment_interval', NumberType::class, [
                'label' => t("Ödeme Periyodu")
            ])
            ->add('trial_period_days', NumberType::class, [
                'label' => t("Deneme Süresi")
            ])
            ->add('amount', NumberType::class, [
                'label' => t("Fiyat")
            ])
            ->add('discount_percent', NumberType::class, [
                'label' => t("İndirim Oranı"),
                'required' => FALSE
            ])
            ->add('is_popular', CheckboxType::class, [
                'label' => t("Popüler Plan Olarak Etiketle"),
                'required' => FALSE
            ])
            ->add('plan_order', NumberType::class, [
                'label' => t("Sıra")
            ])
            ->add('included_features', TextareaType::class, [
                'label' => t("Dahil Özellikler"),
            ])
            ->add('not_included_features', TextareaType::class, [
                'label' => t("Hariç Özellikler"),
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubscriptionPlan::class,
        ]);
    }

}
