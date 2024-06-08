<?php namespace App\Service;

use App\Config\MarketplaceSearchHandlerType;
use App\Entity\Marketplace;
use App\Service\WebScrapingRequestStep\ChangeStep;
use App\Service\WebScrapingRequestStep\KeyDownStep;
use App\Service\WebScrapingRequestStep\KeyUpStep;

class MarketplaceSearchService
{

    public function __construct(private WebScrapingRequestService $webScrapingRequestService)
    {
    }

    public function searchKeyword(string $searchKeyword, Marketplace $marketplace)
    {

        $navigateURL = $marketplace->getRealSearchUrl([
            'keyword' => $searchKeyword
        ]);

        $searchSelectors = explode(Marketplace::SEARCH_SELECTORS_SPLITTER, $marketplace->getSearchSelectors());

        // Create Web Scraping Requests
        $myScrapingService = $this->webScrapingRequestService->clearSteps();

        // Add Steps If Needed
        if ($marketplace->getSearchHandlerType() === MarketplaceSearchHandlerType::STEPS) {
            $myStep1 = new ChangeStep($searchKeyword, $searchSelectors);
            $myStep2 = new KeyDownStep("Enter");
            $myStep3 = new KeyUpStep("Enter");
            $myScrapingService->addStep($myStep1)->addStep($myStep2)->addStep($myStep3);
        }

        if ($marketplace->getSearchHandlerType() === MarketplaceSearchHandlerType::NAVIGATION) {
            // TODO : No Needs
        }

        // Make Request
        $myScrapingService->createRequest($navigateURL);
    }

}