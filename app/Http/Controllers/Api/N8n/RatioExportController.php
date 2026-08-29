<?php

namespace App\Http\Controllers\Api\N8n;

use App\Http\Controllers\Controller;
use App\Models\RatioSnapshot;
use App\Models\ReportPeriod;
use Illuminate\Http\JsonResponse;

class RatioExportController extends Controller
{
    public function index(ReportPeriod $reportPeriod): JsonResponse
    {
        $ratios = RatioSnapshot::with('projectSite')
            ->where('report_period_id', $reportPeriod->id)
            ->get();

        return response()->json([
            'period' => $reportPeriod,
            'ratios' => $ratios,
        ]);
    }
}
