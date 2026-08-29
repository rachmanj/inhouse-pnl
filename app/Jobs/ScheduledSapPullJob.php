<?php

namespace App\Jobs;

use App\Models\ReportPeriod;
use App\Services\Sap\SapAccountBalanceSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScheduledSapPullJob implements ShouldQueue
{
    use Queueable;

    public function handle(SapAccountBalanceSyncService $sync): void
    {
        $period = ReportPeriod::whereIn('status', ['open', 'in_review'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        if (! $period) {
            return;
        }

        $sync->pull($period, 'scheduler');
    }
}
