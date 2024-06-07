<?php namespace App\Controller\Admin\Table;

use App\Entity\Marketplace;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MarketplaceTable extends TableAbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('name', TextColumn::class, [
            'label' => $this->t("Ad"),
            'orderable' => FALSE,
        ]);
        $dataTable->add('url', TextColumn::class, [
            'label' => $this->t("Ana URL"),
            'orderable' => FALSE,
        ]);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $redirectURL = $urlGenerator->generate('app_administrator_marketplace_show', ['theMarketplace' => $value]);
                    return new CrudTableAction($this->t("Göster"), $redirectURL, 'bx bx-show');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $redirectURL = $urlGenerator->generate('app_administrator_marketplace_redirecturl', ['theMarketplace' => $value]);
                    return new CrudTableAction($this->t("Ana URL'ye Git"), $redirectURL, 'bx bx-navigation');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Marketplace::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('x')
                    ->from(Marketplace::class, 'x');
            },
        ]);

        // Add Default Order
        // $dataTable->addOrderBy("created_at", self::DEFAULT_ORDER_DIRECTION_CREATED_AT);

    }


}