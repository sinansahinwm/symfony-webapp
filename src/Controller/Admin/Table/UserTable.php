<?php namespace App\Controller\Admin\Table;

use App\Config\NotificationPriorityType;
use App\Entity\Notification;
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

class UserTable extends TableAbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('email', TextColumn::class, [
            'label' => $this->t("E-Posta"),
        ]);
        $dataTable->add('team', TextColumn::class, [
            'label' => $this->t("Takım"),
            'render' => function ($value, $context) {
                $usersTeam = $context->getTeam();
                if ($usersTeam) {
                    return $usersTeam->getName();
                }
                return "";
            }
        ]);
        $dataTable->add('isVerified', BoolIndicatorColumn::class, [
            'label' => $this->t("E-Posta Onay"),
        ]);
        $dataTable->add('isPassive', BoolIndicatorColumn::class, [
            'label' => $this->t("Aktiflik"),
        ]);

        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $gotoURL = $urlGenerator->generate('app_admin_profile_show', ['theUser' => $value]);
                    return new CrudTableAction($this->t("Görüntüle"), $gotoURL, 'bx bx-user');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => User::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('x')
                    ->from(User::class, 'x');
            },
        ]);

        // Add Default Order
        // $dataTable->addOrderBy("created_at", self::DEFAULT_ORDER_DIRECTION_CREATED_AT);

    }


}