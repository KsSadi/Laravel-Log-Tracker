<?php

namespace Kssadi\LogTracker\Console\Commands;

use Illuminate\Console\Command;
use Kssadi\LogTracker\Services\LogAlertService;

class CheckLogAlertsCommand extends Command
{
    protected $signature = 'log-tracker:check-alerts';

    protected $description = 'Check log files against thresholds and send configured channel alerts';

    public function __construct(private readonly LogAlertService $alertService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('log-tracker.alerts.enabled', false)) {
            $this->info("Log Tracker alerts are disabled. Set 'log-tracker.alerts.enabled' to true to activate.");

            return self::SUCCESS;
        }

        $alerts = $this->alertService->checkAndNotify();

        if (empty($alerts)) {
            $this->info('No alert thresholds exceeded.');

            return self::SUCCESS;
        }

        foreach ($alerts as $alert) {
            $this->warn(sprintf(
                'Alert sent: %s in %s — %d occurrences (threshold exceeded).',
                strtoupper($alert['level']),
                $alert['file'],
                $alert['count'],
            ));
        }

        return self::SUCCESS;
    }
}
