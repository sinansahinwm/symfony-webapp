<?php namespace App\Controller\Admin;

use App\Service\BrowserStepper\BrowserStepper;
use App\Service\BrowserStepper\Step\ClearStepsStep;
use App\Service\BrowserStepper\Step\ClickLinkStep;
use App\Service\BrowserStepper\Step\FilterXPathStep;
use App\Service\BrowserStepper\Step\FormStep;
use App\Service\BrowserStepper\Step\RequestStep;
use App\Service\BrowserStepper\Step\TextCheckStep;
use App\Service\BrowserStepper\Step\XPathCheckStep;
use App\Service\ChromeRecordPlayer\ChromeRecordPlayer;
use App\Service\ChromeRecordPlayer\ChromeRecordReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/test', name: 'app_admin_test_')]
class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ChromeRecordReader $chromeRecordReader, ChromeRecordPlayer $chromeRecordPlayer, BrowserStepper $browserStepper, RequestStep $requestStep, ClickLinkStep $clickLinkStep, FormStep $formStep, TextCheckStep $textCheckStep, XPathCheckStep $selectorMatchesStep, ClearStepsStep $clearStepsStep, FilterXPathStep $filterXPathStep): Response
    {

        $playResult = $chromeRecordPlayer->load("/Users/meehouapp/Desktop/testKaydı.json")->play();
        exit($playResult->html());

        exit("END");
        // Create Steps
        $myRequestStep = $requestStep->setMethod("GET")->setUrl("http://127.0.0.1:8000/auth/signin");
        // $myClickLinkStep = $clickLinkStep->setLinkText("What is Symfony");
        $myFormStep = $formStep->setButtonString("Giriş Yap")->addFormField("auth_signin[email]", "sinansahinwm@gmail.com")->addFormField("auth_signin[password]", "321321321");
        $myTextCheckStep = $textCheckStep->setText("Aydınlık Tema");
        // $mySelectorMatchesStep = $selectorMatchesStep->setSelector('dropdown');

        // Add Steps
        $browserStepper
            ->addStep($myRequestStep)
            // ->addStep($myClickLinkStep)
            ->addStep($myFormStep)
            ->addStep($myTextCheckStep);
        // ->addStep($mySelectorMatchesStep);

        // Get Result Step
        $stepperResponseSuccess = $browserStepper->runSteps()->isSuccess();

        if ($stepperResponseSuccess === TRUE) {

            $myFilterXPathStep = $filterXPathStep->setXPath('/html/body/script[1]');

            $clearSteps = $browserStepper->addStep($clearStepsStep)->addStep($myFilterXPathStep)->runSteps();

            $myLink = $clearSteps->get(BrowserStepper::RETURN_TYPE_HTML, 'defer');

            exit("BİTTİ" . serialize($myLink));
        } else {
            exit($browserStepper->getLastErrorMessage());
        }
        return new JsonResponse([]);
    }

}