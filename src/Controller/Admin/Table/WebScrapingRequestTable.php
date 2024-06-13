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
use DateTime;
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
            'orderable' => TRUE
        ]);
        $dataTable->add('navigate_url', ShowMoreTextColumn::class, [
            'label' => $this->t("URL"),
            'orderable' => FALSE
        ]);
        $dataTable->add('steps', BadgeColumn::class, [
            'label' => $this->t("Adımlar"),
            'className' => "text-center",
            'orderable' => FALSE,
            'type' => function ($theValue) {
                return $theValue === NULL ? 'light' : 'primary';
            },
            'content' => function ($theValue) {
                $badgeText = "--";
                $decodedJSON = is_string($theValue) ? json_decode($theValue) : NULL;
                if (is_array($decodedJSON)) {
                    $stepsCount = count($decodedJSON);
                    $badgeText = $stepsCount . " " . $this->t("adet");
                }
                return $badgeText;
            }
        ]);
        $dataTable->add('consumed_at', FormattedDateTimeColumn::class, [
            'label' => $this->t("Süre"),
            'className' => "text-center",
            'orderable' => FALSE,
            'render' => function ($theValue, $theContext) {
                if ($theContext->getConsumedAt() !== NULL && $theContext->getCreatedAt() !== NULL) {
                    $timeDiffSec = $theContext->getConsumedAt()->getTimestamp() - $theContext->getCreatedAt()->getTimestamp();
                    return '<i class="bx bxs-watch"></i> <sup>' . $timeDiffSec . $this->t("sn") . "</sup>";
                }
                return '<div class="spinner-border spinner-border-sm text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>';
            }
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
                function ($value, UrlGeneratorInterface $urlGenerator) {
                    $gotoURL = $urlGenerator->generate('app_administrator_web_scraping_request_show_html', ['webScrapingRequest' => $value]);
                    return new CrudTableAction($this->t("İçeriği Görüntüle"), $gotoURL, 'bx bxs-file-html');
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