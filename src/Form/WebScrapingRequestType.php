<?php

namespace App\Form;

use App\Entity\WebScrapingRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebScrapingRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('navigate_url')
            ->add('webhook_url')
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebScrapingRequest::class,
        ]);
    }
}
