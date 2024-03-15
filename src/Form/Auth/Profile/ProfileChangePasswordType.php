<?php

namespace App\Form\Auth\Profile;

use App\Entity\User;
use App\Form\AbstractFormType;
use App\Form\Auth\AuthSignupType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Symfony\Component\Translation\t;

class ProfileChangePasswordType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('oldPassword', PasswordType::class, [
            'required' => TRUE,
            'label' => t("Eski Şifreniz"),
            'mapped' => FALSE,
            'attr' => [
                'placeholder' => "********"
            ]
        ]);

        $builder->add('newPassword', RepeatedType::class, [
            'required' => TRUE,
            'label' => t("Yeni Şifreniz"),
            'mapped' => FALSE,
            'type' => PasswordType::class,
            'first_options' => [
                'label' => t("Yeni Şifreniz"),
                'attr' => [
                    'placeholder' => "********"
                ],
            ],
            'second_options' => [
                'label' => t("Yeni Şifreniz (Tekrar)"),
                'attr' => [
                    'placeholder' => "********"
                ],
            ],
            'invalid_message' => t("Yeni şifreler birbiriyle uyuşmuyor."),
            'constraints' => [
                new NotBlank([
                    'message' => t('Lütfen bir şifre belirleyin.'),
                ]),
                new Length([
                    'min' => AuthSignupType::PASSWORD_MIN_LENGTH,
                    'minMessage' => t('Şifreniz en az %limit% karakter uzunluğunda olmalıdır.', ["%limit%" => AuthSignupType::PASSWORD_MIN_LENGTH]),
                    'max' => AuthSignupType::PASSWORD_MAX_LENGTH,
                ]),
            ],
        ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
