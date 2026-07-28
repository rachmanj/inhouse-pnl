<?php

namespace App\Jobs;

use App\Models\ReportPeriod;
use App\Services\Sap\SapAccountBalanceSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduledSapPullJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SapAccountBalanceSyncService $sync): void
    {
        $period = ReportPeriod::where('status', 'open')->orderByDesc('id')->first();
        if ($period) {
            $sync->pull($period);
        }
    }
}
