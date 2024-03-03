<?php

namespace App\Form\Auth\Profile;

use App\Entity\User;
use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileMakePassiveType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('boolConfirmation', CheckboxType::class, [
            'label' => $this->t("Onaylıyorum, oluşabilecek durumların farkındayım."),
            'required' => TRUE,
            'mapped' => FALSE
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
