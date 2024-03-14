<?php namespace App\Controller\Admin\Table;

use App\Entity\PuppeteerReplay;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PuppeteerReplayTable extends AbstractController implements DataTableTypeInterface
{

    public function __construct(private TranslatorInterface $translatorBag, private Security $security)
    {
    }

    public function configure(DataTable $dataTable, array $options): void
    {

        $loggedUser = $this->security->getUser();

        $dataTable->add('file_name', TextColumn::class);
        $dataTable->add('created_at', TextColumn::class);
        $dataTable->add('status', TextColumn::class);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction($this->translatorBag->trans('Göster'), 'https://www.google.com', 'bx bx-home');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction($this->translatorBag->trans('Sil'), $urlGenerator->generate('app_admin_puppeteer_replay_delete', ["id" => $value]), 'bx bx-trash');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => PuppeteerReplay::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder, $loggedUser) {
                $builder->select('x')->from(PuppeteerReplay::class, 'x')
                    ->where('x.created_by = :param1')
                    ->setParameter('param1', $loggedUser);
            },
        ]);

    }


}