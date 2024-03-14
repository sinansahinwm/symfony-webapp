<?php

namespace App\Form;

use App\Entity\AbstractFile;
use App\Entity\PuppeteerReplay;
use App\Repository\PuppeteerReplayHookRecordRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class PuppeteerReplayType extends AbstractFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $helperText = $this->t("Maksimum dosya boyutu: ") . '1MB';

        $builder->add('theFile', DropzoneType::class, [
            'label' => $this->t('Dosya'),
            'attr' => [
                'placeholder' => $this->t('Sürükleyip bırakın veya göz atın'),
            ],
            'help' => $helperText,
            'help_attr' => [
                'class' => "text-warning"
            ]
        ]);

        $builder->add('theFile', DropzoneType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PuppeteerReplay::class,
        ]);
    }
}
