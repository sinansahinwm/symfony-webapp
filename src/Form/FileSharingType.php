<?php

namespace App\Form;

use App\Controller\Admin\Select\TeamMatesSelectController;
use App\Entity\AbstractFile;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Dropzone\Form\DropzoneType;

class FileSharingType extends AbstractFormType
{
    public function __construct(TranslatorInterface $translator, private TeamMatesSelectController $teamMatesSelectController, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('fileName', DropzoneType::class, [
            'label' => $this->t('Dosya')
        ]);
        $builder->add('password', PasswordType::class, [
            'label' => $this->t('Şifre'),
            'required' => FALSE,
            'attr' => [
                'placeholder' => $this->t('Şifresiz paylaşmak için boş bırakın.')
            ]
        ]);
        $builder->add('toUser', EntityType::class, [
            'label' => $this->t('Şu Kişilerle Paylaş'),
            'attr' => [
                'class' => 'select2',
                'data-ajax--url' => $this->teamMatesSelectController::CALLBACK_PATH,
                'data-placeholder' => $this->t("Kullanıcı seçimi yapınız")
            ],
            'multiple' => TRUE,
            'mapped' => FALSE,
            'class' => User::class,
            'query_builder' => function (EntityRepository $entityRepository): QueryBuilder {
                return $this->teamMatesSelectController->queryBuilder($entityRepository);
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbstractFile::class,
        ]);
    }
}
