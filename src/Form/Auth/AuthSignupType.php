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
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthSignupType extends AbstractFormType
{

    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_MAX_LENGTH = 20;


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->t('E-Posta'),
                'attr' => [
                    'autofocus' => 'autofocus',
                    'placeholder' => 'E-Posta adresinizi girin'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => "showModalOnCheck",
                    'modalId' => "userAgreementModal"
                ],
                'label' => $this->t('Kullanım koşullarını onaylıyorum.'),
                'constraints' => [
                    new IsTrue([
                        'message' => $this->t('Kullanım koşullarını onaylamak zorunludur.'),
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => $this->t('Şifre'),
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '********'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->t('Lütfen bir şifre belirleyin.'),
                    ]),
                    new Length([
                        'min' => self::PASSWORD_MIN_LENGTH,
                        'minMessage' => $this->t('Şifreniz en az %limit% karakter uzunluğunda olmalıdır.', ["%limit%" => self::PASSWORD_MIN_LENGTH]),
                        'max' => self::PASSWORD_MAX_LENGTH,
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
