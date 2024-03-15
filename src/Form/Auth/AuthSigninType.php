<?php

namespace App\Form\Auth;

use App\Entity\User;
use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class AuthSigninType extends AbstractFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => t('E-Posta'),
                'attr' => [
                    'autofocus' => "autofocus",
                    'placeholder' => t('E-Posta adresinizi girin')
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => t('Şifre'),
                'attr' => [
                    'placeholder' => '********'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
