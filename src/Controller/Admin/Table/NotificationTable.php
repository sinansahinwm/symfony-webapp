<?php namespace App\Controller\Admin\Table;

use App\Entity\User;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use Closure;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationTable extends AbstractController implements DataTableTypeInterface
{

    public function __construct(TranslatorInterface $translatorBag)
    {
    }

    public function configure(DataTable $dataTable, array $options): void
    {

        $dataTable->add('email', TextColumn::class);
        $dataTable->add('phone', TextColumn::class);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function (UrlGeneratorInterface $urlGenerator, $value) {
                    return new CrudTableAction("Göster", 'https://www.google.com', 'bx bx-home');
                },
                function (UrlGeneratorInterface $urlGenerator, $value) {
                    return new CrudTableAction("Sil", 'https://www.google.com', 'bx bx-home');
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
                $builder->select('x')->from(User::class, 'x');
            },
        ]);

    }


}