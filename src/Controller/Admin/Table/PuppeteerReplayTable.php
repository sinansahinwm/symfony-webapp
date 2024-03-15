<?php namespace App\Controller\Admin\Table;

use App\Config\PuppeteerReplayStatusType;
use App\Entity\PuppeteerReplay;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\BadgeColumn;
use App\Service\CrudTable\CrudTableAction;
use App\Service\CrudTable\DisableCachingCriteriaProvider;
use App\Service\CrudTable\FormattedDateTimeColumn;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\Translation\t;

class PuppeteerReplayTable extends AbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('fileName', TextColumn::class, [
            'orderable' => FALSE,
        ]);
        $dataTable->add('created_at', FormattedDateTimeColumn::class, [
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
                    PuppeteerReplayStatusType::UPLOAD => t("Yüklendi"),
                    PuppeteerReplayStatusType::PROCESSING => t("İşleniyor"),
                    PuppeteerReplayStatusType::COMPLETED => t("Tamamlandı"),
                    PuppeteerReplayStatusType::ERROR => t("Hata"),
                    default => t("Bilinmiyor"),
                };
            }
        ]);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction(t('Göster'), $urlGenerator->generate("app_admin_puppeteer_replay_show", ["id" => $value]), 'bx bx-chevron-right');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction(t('Sil'), $urlGenerator->generate("app_admin_puppeteer_replay_delete", ["id" => $value]), 'bx bx-trash');
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