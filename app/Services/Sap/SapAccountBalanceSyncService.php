<?php

namespace App\Services\Sap;

use App\Models\ReportPeriod;
use App\Models\SapSyncRun;

class SapAccountBalanceSyncService
{
    public function pull(ReportPeriod $period): SapSyncRun
    {
        $run = SapSyncRun::create([
            'report_period_id' => $period->id,
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            // Read-only SAP DB query placeholder — requires sap connection configured
            $run->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $run->update([
                'status' => 'failed',
                'error_summary' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }

        return $run;
    }
}
