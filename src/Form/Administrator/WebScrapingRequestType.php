<?php

namespace App\Form\Administrator;

use App\Entity\WebScrapingRequest;
use App\Form\AbstractFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class WebScrapingRequestType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('navigate_url', UrlType::class, [
                'label' => t("URL")
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebScrapingRequest::class,
        ]);
    }
}
