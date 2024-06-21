<?php namespace App\Controller\Admin\Table;

use App\Entity\Product;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use App\Service\CrudTable\ImageColumn;
use App\Service\CrudTable\ShowMoreTextColumn;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductTable extends TableAbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('image', ImageColumn::class, [
            'label' => $this->t("Görsel"),
            'orderable' => FALSE,
        ]);
        $dataTable->add('marketplace', TextColumn::class, [
            'label' => $this->t("Pazaryeri"),
            'orderable' => TRUE,
            'render' => function ($value, $context) {
                return $context->getMarketplace()->getName();
            }
        ]);
        $dataTable->add('identity', TextColumn::class, [
            'label' => $this->t("Kimlik"),
            'orderable' => FALSE
        ]);
        $dataTable->add('name', ShowMoreTextColumn::class, [
            'label' => $this->t("Ad"),
            'orderable' => FALSE,
            'slice' => 50
        ]);

        $dataTable->add('id', ActionsColumn::class, [
            'target_blank' => TRUE,
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $theURL = $urlGenerator->generate('app_administrator_product_go_to_product', ['theProduct' => $value]);
                    return new CrudTableAction($this->t("Ürüne Git"), $theURL, 'bx bx-link');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $theURL = $urlGenerator->generate('app_administrator_product_go_to_product_marketplace', ['theProduct' => $value]);
                    return new CrudTableAction($this->t("Pazaryerine Git"), $theURL, 'bx bx-store-alt');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $theURL = $urlGenerator->generate('app_administrator_marketplace_search_keyword_by_product', ['theProduct' => $value]);
                    return new CrudTableAction($this->t("Anahtar Kelime Araştır"), $theURL, 'bx bx-search');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Product::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('x')
                    ->from(Product::class, 'x');
            },
        ]);

        // Add Default Order
        // $dataTable->addOrderBy("created_at", self::DEFAULT_ORDER_DIRECTION_CREATED_AT);

    }


}