<?php namespace App\Controller\Admin\Table;

use App\Config\PuppeteerReplayStatusType;
use App\Entity\PuppeteerReplay;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\BadgeColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PuppeteerReplayTable extends AbstractController implements DataTableTypeInterface
{

    public function __construct(private TranslatorInterface $translatorBag)
    {
    }

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('file_name', TextColumn::class, [
            'orderable' => FALSE,
        ]);
        $dataTable->add('created_at', DateTimeColumn::class, [
            'orderable' => FALSE
        ]);
        $dataTable->add('status', BadgeColumn::class, [
            'type' => function ($value) {
                return match ($value) {
                    PuppeteerReplayStatusType::UPLOAD => "secondary",
                    PuppeteerReplayStatusType::PROCESSING => "info",
                    PuppeteerReplayStatusType::COMPLETED => "success",
                    PuppeteerReplayStatusType::ERROR => "danger",
                    default => 'secondary',
                };
            },
            'content' => function ($value) {
                return match ($value) {
                    PuppeteerReplayStatusType::UPLOAD => $this->translatorBag->trans("Yüklendi"),
                    PuppeteerReplayStatusType::PROCESSING => $this->translatorBag->trans("İşleniyor"),
                    PuppeteerReplayStatusType::COMPLETED => $this->translatorBag->trans("Tamamlandı"),
                    PuppeteerReplayStatusType::ERROR => $this->translatorBag->trans("Hata"),
                    default => $this->translatorBag->trans("Bilinmiyor"),
                };
            }
        ]);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction($this->translatorBag->trans('Göster'), $urlGenerator->generate("app_admin_puppeteer_replay_show", ["id" => $value]), 'bx bx-home');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction($this->translatorBag->trans('Sil'), $urlGenerator->generate("app_admin_puppeteer_replay_delete", ["id" => $value]), 'bx bx-trash');
                },
            ]
        ]);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => PuppeteerReplay::class,
            'criteria' => [
                new DisableCachingCriteriaProvider(),
                new SearchCriteriaProvider(),
            ],
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('x')
                    ->from(PuppeteerReplay::class, 'x')
                    ->orderBy('x.created_by', 'DESC');
            },
        ]);

    }


}