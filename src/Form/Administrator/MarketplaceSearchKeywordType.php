<?php namespace App\Form\Administrator;

use App\Entity\Marketplace;
use App\Form\AbstractFormType;
use App\Repository\MarketplaceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class MarketplaceSearchKeywordType extends AbstractFormType
{
    public function __construct(private MarketplaceRepository $marketplaceRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'label' => t("Arama Kelimesi"),
                'mapped' => FALSE
            ])
            ->add('marketplaces', EntityType::class, [
                'label' => t("Pazaryerleri"),
                'class' => Marketplace::class,
                'multiple' => TRUE,
                'mapped' => FALSE,
                'attr' => [
                    'class' => "select2",
                ],
                'data' => $this->marketplaceRepository->findAll(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Marketplace::class,
        ]);
    }
}
