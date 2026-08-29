<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnomalyAlert;
use App\Models\ReconciliationCheck;
use App\Models\ReportPeriod;
use App\Models\VarianceFlag;
use Illuminate\Http\JsonResponse;

class InsightsApiController extends Controller
{
    public function index(ReportPeriod $reportPeriod): JsonResponse
    {
        $variances = VarianceFlag::with(['projectSite', 'pnlLine'])
            ->where('report_period_id', $reportPeriod->id)
            ->whereIn('severity', ['warning', 'critical'])
            ->get();

        $anomalies = AnomalyAlert::with(['projectSite', 'account'])
            ->where('report_period_id', $reportPeriod->id)
            ->where('status', 'open')
            ->get();

        $reconciliations = ReconciliationCheck::whereHas('importBatch', fn ($q) => $q->where('report_period_id', $reportPeriod->id))
            ->where('is_reconciled', false)
            ->get();

        return response()->json([
            'period' => $reportPeriod,
            'variances' => $variances,
            'anomalies' => $anomalies,
            'reconciliations' => $reconciliations,
        ]);
    }
}
