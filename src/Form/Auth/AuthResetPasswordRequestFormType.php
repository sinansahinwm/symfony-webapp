<?php

namespace App\Form\Auth;

use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthResetPasswordRequestFormType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email', 'placeholder' => $this->t('E-Posta adresinizi girin'),],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->t('Lütfen e-posta adresinizi giriniz.'),
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
