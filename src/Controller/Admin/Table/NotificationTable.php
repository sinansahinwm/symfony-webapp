<?php namespace App\Controller\Admin\Table;

use App\Config\NotificationPriorityType;
use App\Entity\Notification;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\BadgeColumn;
use App\Service\CrudTable\BoolIndicatorColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use App\Service\CrudTable\FormattedDateTimeColumn;
use App\Service\CrudTable\ShowMoreTextColumn;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotificationTable extends TableAbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('created_at', FormattedDateTimeColumn::class, [
            'label' => $this->t("Yaratılma Zamanı"),
            'orderable' => TRUE,
        ]);
        $dataTable->add('priority', BadgeColumn::class, [
            'orderable' => FALSE,
            'searchable' => FALSE,
            'globalSearchable' => FALSE,
            'type' => function ($value) {
                return match ($value) {
                    NotificationPriorityType::LOW => "label-secondary",
                    NotificationPriorityType::NORMAL => "secondary",
                    NotificationPriorityType::HIGH => "danger",
                    default => 'label-secondary',
                };
            },
            'content' => function ($value) {
                return match ($value) {
                    NotificationPriorityType::LOW => $this->t("Düşük"),
                    NotificationPriorityType::NORMAL => $this->t("Normal"),
                    NotificationPriorityType::HIGH => $this->t("Yüksek"),
                    default => $this->t("Bilinmiyor"),
                };
            }
        ]);
        $dataTable->add('content', ShowMoreTextColumn::class, [
            'orderable' => FALSE,
            'label' => $this->t("İçerik"),
        ]);
        $dataTable->add('is_read', BoolIndicatorColumn::class);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $gotoURL = $urlGenerator->generate('app_admin_notification_show', ['notification' => $value]);
                    return new CrudTableAction($this->t("Bildirimi Görüntüle"), $gotoURL, 'bx bx-envelope');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $removeURL = $urlGenerator->generate('app_admin_notification_remove', ['notification' => $value]);
                    return new CrudTableAction($this->t("Sil"), $removeURL, 'bx bx-trash');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Notification::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('x')
                    ->from(Notification::class, 'x')
                    ->where('x.to_user = :param1')
                    ->setParameter('param1', $this->getUser());
            },
        ]);

        // Add Default Order
        $dataTable->addOrderBy("created_at", self::DEFAULT_ORDER_DIRECTION_CREATED_AT);

    }


}