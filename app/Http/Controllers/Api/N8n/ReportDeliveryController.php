<?php

namespace App\Http\Controllers\Api\N8n;

use App\Http\Controllers\Controller;
use App\Jobs\DeliverReportPackageJob;
use App\Models\ReportPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportDeliveryController extends Controller
{
    public function deliver(Request $request, ReportPackage $reportPackage): JsonResponse
    {
        $validated = $request->validate([
            'channel' => ['nullable', 'in:email,whatsapp,telegram'],
            'recipient' => ['nullable', 'string'],
        ]);

        DeliverReportPackageJob::dispatch(
            $reportPackage,
            $validated['channel'] ?? 'email',
            $validated['recipient'] ?? null
        );

        return response()->json(['status' => 'queued', 'package_id' => $reportPackage->id]);
    }
}
