<?php

namespace App\Services\Intelligence;

use App\Models\PnlSnapshot;
use App\Models\VarianceFlag;

class VarianceAnalysisService
{
    public function analyze(PnlSnapshot $snapshot): void
    {
        // Placeholder — full implementation compares baseline vs current snapshots
        VarianceFlag::where('report_period_id', $snapshot->report_period_id)
            ->where('project_site_id', $snapshot->project_site_id)
            ->delete();
    }
}
