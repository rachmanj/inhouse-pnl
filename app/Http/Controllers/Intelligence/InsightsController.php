<?php

namespace App\Http\Controllers\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\AnomalyAlert;
use App\Models\ReportPeriod;
use App\Models\VarianceFlag;
use Inertia\Inertia;

class InsightsController extends Controller
{
    public function index(ReportPeriod $reportPeriod)
    {
        $variances = VarianceFlag::where('report_period_id', $reportPeriod->id)
            ->whereIn('severity', ['warning', 'critical'])
            ->latest()
            ->get();

        $anomalies = AnomalyAlert::where('report_period_id', $reportPeriod->id)
            ->where('status', 'open')
            ->latest()
            ->get();

        return Inertia::render('Intelligence/Insights', [
            'period' => $reportPeriod,
            'insights' => $variances->concat($anomalies),
        ]);
    }
}
