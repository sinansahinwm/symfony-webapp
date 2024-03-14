<?php

namespace App\Form;

use App\Entity\PuppeteerReplay;
use App\Repository\PuppeteerReplayHookRecordRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class PuppeteerReplayType extends AbstractType
{

    public function __construct(private PuppeteerReplayHookRecordRepository $hookRecordRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('theFile', DropzoneType::class);
        $builder->add('field', CKEditorType::class, array(
            'mapped' => FALSE,
            'config_name' => 'app_xpath_selector',
            "data" => $this->hookRecordRepository->find(24)->getContent()
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PuppeteerReplay::class,
        ]);
    }
}
