<?php

namespace App\Form\Auth;

use App\Entity\User;
use App\Form\AbstractFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthSigninType extends AbstractFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->t('E-Posta'),
                'attr' => [
                    'autofocus' => "autofocus",
                    'placeholder' => $this->t('E-Posta adresinizi girin')
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => $this->t('Şifre'),
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
