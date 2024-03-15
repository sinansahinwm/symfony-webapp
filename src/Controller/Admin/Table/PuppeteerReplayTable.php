<?php namespace App\Controller\Admin\Table;

use App\Config\PuppeteerReplayStatusType;
use App\Entity\PuppeteerReplay;
use App\Service\CrudTable\ActionsColumn;
use App\Service\CrudTable\BadgeColumn;
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

class PuppeteerReplayTable extends TableAbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options): void
    {
        $dataTable->add('fileName', ShowMoreTextColumn::class, [
            'label' => $this->t("Dosya Adı"),
            'orderable' => FALSE,
        ]);
        $dataTable->add('created_at', FormattedDateTimeColumn::class, [
            'label' => $this->t("Yaratılma Zamanı"),
            'orderable' => FALSE
        ]);
        $dataTable->add('status', BadgeColumn::class, [
            'label' => $this->t("Durum"),
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
                    PuppeteerReplayStatusType::UPLOAD => $this->t("Yüklendi"),
                    PuppeteerReplayStatusType::PROCESSING => $this->t("İşleniyor"),
                    PuppeteerReplayStatusType::COMPLETED => $this->t("Tamamlandı"),
                    PuppeteerReplayStatusType::ERROR => $this->t("Hata"),
                    default => $this->t("Bilinmiyor"),
                };
            }
        ]);
        $dataTable->add('id', ActionsColumn::class, [
            'actions' => [
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction($this->t('Göster'), $urlGenerator->generate("app_admin_puppeteer_replay_show", ["puppeteerReplay" => $value]), 'bx bx-chevron-right');
                },
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    return new CrudTableAction($this->t('Sil'), $urlGenerator->generate("app_admin_puppeteer_replay_delete", ["puppeteerReplay" => $value]), 'bx bx-trash');
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