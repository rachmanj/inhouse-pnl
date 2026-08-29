<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Pnl\PnlAggregationService;
use App\Services\Pnl\SitePnlPresenter;
use Illuminate\Http\JsonResponse;

class PnlDataController extends Controller
{
    public function site(ProjectSite $projectSite, ReportPeriod $reportPeriod, SitePnlPresenter $presenter, PnlAggregationService $aggregation): JsonResponse
    {
        $baselinePeriod = ReportPeriod::where('year', $reportPeriod->baseline_year)
            ->where('month', $reportPeriod->month)
            ->first();

        $current = $aggregation->aggregateSite($reportPeriod, $projectSite);
        $baseline = $baselinePeriod
            ? $aggregation->aggregateSite($baselinePeriod, $projectSite)
            : $current;

        return response()->json($presenter->present($baseline, $current, request('view', 'pnl')));
    }

    public function consolidated(ReportPeriod $reportPeriod, PnlAggregationService $aggregation): JsonResponse
    {
        $snapshot = $aggregation->aggregateConsolidated($reportPeriod);

        return response()->json([
            'period' => $reportPeriod,
            'snapshot' => $snapshot->load('lines.pnlLine'),
        ]);
    }
}
