<?php namespace App\CronTask;

use App\Message\AppEmailMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCronTask('@daily', jitter: 60)]
class DailyReportsTask
{

    public function __construct(private MessageBusInterface $messageBus, private ContainerBagInterface $containerBag, private TranslatorInterface $translator)
    {
    }

    public function __invoke(): void
    {
        $adminEmail = $this->getAdministratorEmail();

        $dailyReportsContext = $this->getDailyReportsContext();
        $myDailyReportEmailMessage = new AppEmailMessage('daily_reports', $adminEmail, $this->translator->trans('Günlük Rapor'), $dailyReportsContext);
        $this->messageBus->dispatch($myDailyReportEmailMessage);
    }

    private function getAdministratorEmail(): string
    {
        return $this->containerBag->get("app.fixtures.administrator.email");
    }

    private function getDailyReportsContext(): array
    {
        return []; // TODO : prepare daily tasks context
    }
}