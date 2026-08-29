<?php

namespace App\Http\Controllers\Api\N8n;

use App\Http\Controllers\Controller;
use App\Models\TaxFiling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaxFilingRadarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $days = $request->integer('days', 30);

        $filings = TaxFiling::with(['projectSite', 'reportPeriod'])
            ->where('status', 'pending')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->orderBy('due_date')
            ->get();

        return response()->json(['filings' => $filings]);
    }
}
