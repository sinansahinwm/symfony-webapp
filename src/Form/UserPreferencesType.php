<?php

namespace App\Form;

use App\Entity\UserPreferences;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPreferencesType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receive_emails', CheckboxType::class, [
                'label' => t("E-Posta bildirimlerini almak istiyorum."),
                'required' => FALSE,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserPreferences::class,
        ]);
    }
}
