<?php

namespace App\Http\Controllers\Api\N8n;

use App\Http\Controllers\Controller;
use App\Jobs\DeliverReportPackageJob;
use App\Jobs\ScheduledSapPullJob;
use App\Models\ReportPackage;
use App\Models\ReportPeriod;
use App\Models\RatioSnapshot;
use App\Models\TaxFiling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SapSyncController extends Controller
{
    public function trigger(Request $request): JsonResponse
    {
        ScheduledSapPullJob::dispatch();

        return response()->json(['status' => 'queued']);
    }
}
