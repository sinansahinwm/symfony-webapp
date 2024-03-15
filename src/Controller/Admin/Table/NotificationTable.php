<?php namespace App\Controller\Admin\Table;

use App\Config\NotificationPriorityType;
use App\Entity\Notification;
use App\Entity\User;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\BadgeColumn;
use App\Service\CrudTable\BoolIndicatorColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use App\Service\CrudTable\FormattedDateTimeColumn;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Assert\Not;
use function Symfony\Component\Translation\t;

class NotificationTable extends AbstractController implements DataTableTypeInterface
{
    public function configure(DataTable $dataTable, array $options): void
    {

        $dataTable->add('created_at', FormattedDateTimeColumn::class);
        $dataTable->add('priority', BadgeColumn::class, [
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
                    NotificationPriorityType::LOW => t("Düşük"),
                    NotificationPriorityType::NORMAL => t("Normal"),
                    NotificationPriorityType::HIGH => t("Yüksek"),
                    default => t("Bilinmiyor"),
                };
            }
        ]);
        $dataTable->add('content', TextColumn::class);
        $dataTable->add('is_read', BoolIndicatorColumn::class);
        $dataTable->add('url', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction(t("Bildirimi Görüntüle"), $value, 'bx bx-envelope');
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
                    ->orderBy('x.created_at', 'DESC')
                    ->where('x.to_user = :param1')
                    ->setParameter('param1', $this->getUser());
            },
        ]);

    }


}