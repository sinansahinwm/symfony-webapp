<?php

namespace App\Form;

use App\Entity\PuppeteerReplay;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;
use function Symfony\Component\Translation\t;

class PuppeteerReplayType extends AbstractFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $helperText = t("Maksimum dosya boyutu: ") . '1MB';
        $builder->add('theFile', DropzoneType::class, [
            'label' => t('Dosya'),
            'attr' => [
                'placeholder' => t('Sürükleyip bırakın veya göz atın'),
            ],
            'help' => $helperText,
            'help_attr' => [
                'class' => "text-warning"
            ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PuppeteerReplay::class,
        ]);
    }
}
