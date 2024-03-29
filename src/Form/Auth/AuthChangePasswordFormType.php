<?php

namespace App\Form\Auth;

use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Symfony\Component\Translation\t;

class AuthChangePasswordFormType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => t('Lütfen bir şifre belirtiniz.'),
                        ]),
                        new Length([
                            'min' => AuthSignupType::PASSWORD_MIN_LENGTH,
                            'minMessage' => t('Şifreniz en az %limit% karakter uzunluğunda olmalıdır.', ['limit' => AuthSignupType::PASSWORD_MIN_LENGTH]),
                            'max' => AuthSignupType::PASSWORD_MAX_LENGTH,
                        ]),
                    ],
                    'label' => t('Yeni Şifreniz'),
                ],
                'second_options' => [
                    'label' => t('Şifrenizi Tekrarlayın'),
                ],
                'invalid_message' => t('Şifre alanları birbiriyle uyuşmuyor.'),
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
