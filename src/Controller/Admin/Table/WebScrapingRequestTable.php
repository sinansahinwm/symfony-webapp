<?php namespace App\Controller\Admin\Table;

use App\Config\NotificationPriorityType;
use App\Config\WebScrapingRequestStatusType;
use App\Entity\Notification;
use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Entity\WebScrapingRequest;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\BadgeColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use App\Service\CrudTable\FormattedDateTimeColumn;
use App\Service\CrudTable\ShowMoreTextColumn;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WebScrapingRequestTable extends TableAbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('created_at', FormattedDateTimeColumn::class, [
            'label' => $this->t("Zaman Damgası"),
            'orderable' => FALSE
        ]);
        $dataTable->add('navigate_url', ShowMoreTextColumn::class, [
            'label' => $this->t("URL"),
            'orderable' => FALSE
        ]);
        $dataTable->add('status', BadgeColumn::class, [
            'label' => $this->t("Durum"),
            'className' => "text-center",
            'orderable' => FALSE,
            'type' => function ($theValue) {
                if ($theValue === WebScrapingRequestStatusType::COMPLETED) {
                    return "success";
                }
                return "secondary";
            },
            'content' => function ($theValue) {
                return $theValue;
            }
        ]);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $gotoURL = $urlGenerator->generate('app_administrator_web_scraping_request_delete', ['webScrapingRequest' => $value]);
                    return new CrudTableAction($this->t("Sil"), $gotoURL, 'bx bx-trash');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => WebScrapingRequest::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('x')
                    ->from(WebScrapingRequest::class, 'x');
            },
        ]);

        // Add Default Order
        // $dataTable->addOrderBy("planOrder", self::DEFAULT_ORDER_DIRECTION_CREATED_AT);

    }


}