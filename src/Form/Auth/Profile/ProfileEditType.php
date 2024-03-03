<?php

namespace App\Form\Auth\Profile;

use App\Entity\User;
use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileEditType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [
            'label' => $this->t("E-Posta"),
            'mapped' => FALSE,
            'disabled' => TRUE,
            'attr' => [
                'read_only' => TRUE,
                'value' => $options["readonlyValue"]
            ]
        ]);
        $builder->add('display_name', TextType::class, [
            'label' => $this->t("Kullanıcı Adı"),
            'required' => TRUE,
            'attr' => [
                'placeholder' => $this->t("Örn; John DOE"),
            ]
        ]);
        $builder->add('phone', TelType::class, [
            'label' => $this->t("Telefon Nu"),
            'required' => TRUE,
            'attr' => [
                'placeholder' => $this->t("Örn; +904461736675"),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'readonlyValue' => ''
        ]);
        $resolver->setAllowedTypes('readonlyValue', 'string');
    }
}
