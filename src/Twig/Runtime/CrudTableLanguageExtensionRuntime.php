<?php

namespace App\Twig\Runtime;

use Omines\DataTablesBundle\DataTable;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class CrudTableLanguageExtensionRuntime implements RuntimeExtensionInterface
{

    private $defaultTranslationDomain;

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getCrudTableLanguage(DataTable $dataTable): string
    {
        $this->defaultTranslationDomain = $dataTable->getTranslationDomain();

        $dataTableLanguage = [
            "decimal" => ".",
            "emptyTable" => "Tabloda veri yok",
            "info" => $this->t('_TOTAL_ adet ögeden _START_ ile _END_ arası gösteriliyor'),
            "infoEmpty" => $this->t('0 ögeden 0 ila 0 arası gösteriliyor'),
            "infoFiltered" => "(_MAX_ adet öge arasından filtrelendi)",
            "infoPostFix" => "",
            "thousands" => "",
            "lengthMenu" => $this->t("_MENU_ Öge Göster"),
            "loadingRecords" => $this->t("Yükleniyor..."),
            "processing" => $this->t("İşleniyor..."),
            "search" => $this->t("Ara:"),
            "zeroRecords" => $this->t("Eşleşen hiçbir kayıt bulunamadı"),
            "paginate" => [
                "first" => $this->t("İlk"),
                "last" => $this->t("Son"),
                "next" => $this->t("Sonraki"),
                "previous" => $this->t("Önceki")
            ],
            "aria" => [
                "orderable" => $this->t("Bu sütuna göre sırala"),
                "orderableReverse" => $this->t("Bu sütunu ters sırala")
            ]
        ];
        return json_encode($dataTableLanguage);
    }

    public function t(string $id): string
    {
        return $this->translator->trans($id, [], $this->defaultTranslationDomain);
    }

}
