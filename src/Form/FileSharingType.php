<?php

namespace App\Form;

use App\Controller\Admin\Select\TeamMatesSelectController;
use App\Entity\AbstractFile;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class FileSharingType extends AbstractFormType
{
    public function __construct(TranslatorInterface $translator, private TeamMatesSelectController $teamMatesSelectController, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $helperText = $this->t("Maksimum dosya boyutu: ") . AbstractFile::ALLOWED_MAX_FILE_SIZE_MB . 'MB';

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
        $builder->add('publisherNotes', TextareaType::class, [
            'label' => $this->t("Notlarınız"),
            'help' => $this->t("Bu notlar gönderilen e-postanın alt bölümünde paylaşılan kullanıcıya gösterilecektir."),
            'mapped' => FALSE
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbstractFile::class,
        ]);
    }
}
