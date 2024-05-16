<?php namespace App\Controller\Admin\Table;

use App\Config\NotificationPriorityType;
use App\Entity\Notification;
use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\BadgeColumn;
use App\Service\CrudTable\BoolIndicatorColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SubscriptionPlanTable extends TableAbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('name', TextColumn::class, [
            'label' => $this->t("Plan Adı"),
            'orderable' => FALSE
        ]);
        $dataTable->add('amount', TextColumn::class, [
            'label' => $this->t("Fiyat"),
            'className' => "text-center"
        ]);
        $dataTable->add('currency', TextColumn::class, [
            'label' => $this->t("Kur"),
            'className' => "text-center",
            'orderable' => FALSE
        ]);
        $dataTable->add('currency_sign', TextColumn::class, [
            'label' => $this->t("Kur Simgesi"),
            'className' => "text-center",
            'orderable' => FALSE
        ]);
        $dataTable->add('discount_percent', TextColumn::class, [
            'label' => $this->t("İndirim Oranı (%)"),
            'className' => "text-center"
        ]);
        $dataTable->add('payment_interval', TextColumn::class, [
            'label' => $this->t("Ödeme Sıklığı (gün)"),
            'className' => "text-center"
        ]);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $gotoURL = $urlGenerator->generate('app_admin_subscription_plans');
                    return new CrudTableAction($this->t("Sayfayı Yenile"), $gotoURL, 'bx bx-refresh');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => SubscriptionPlan::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('x')
                    ->from(SubscriptionPlan::class, 'x');
            },
        ]);

        // Add Default Order
        // $dataTable->addOrderBy("planOrder", self::DEFAULT_ORDER_DIRECTION_CREATED_AT);

    }


}