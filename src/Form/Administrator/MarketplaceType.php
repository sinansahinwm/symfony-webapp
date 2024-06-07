<?php namespace App\Form\Administrator;

use App\Config\MarketplaceSearchHandlerType;
use App\Entity\Marketplace;
use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\Translation\t;

class MarketplaceType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t("Ad")
            ])
            ->add('url', UrlType::class, [
                'label' => t("Ana URL")
            ])
            ->add('logo', UrlType::class, [
                'label' => t("Logo")
            ])
            ->add('search_url', UrlType::class, [
                'label' => t("Arama URL")
            ])
            ->add('search_selectors', TextareaType::class, [
                'label' => t("Arama Seçicileri"),
                'help' => t("Ve Bağlacı İçin Şunu Kullanın: ") . Marketplace::SEARCH_SELECTORS_SPLITTER,
                'required' => FALSE
            ])
            ->add('search_handler_type', ChoiceType::class, [
                'label' => t("Arama Yöntemi"),
                'choices' => MarketplaceSearchHandlerType::getValuesArray()
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marketplace::class,
        ]);
    }
}
