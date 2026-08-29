<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImportBatch;
use App\Models\SapStagingRow;
use Illuminate\Http\JsonResponse;

class ImportPreviewController extends Controller
{
    public function show(ImportBatch $importBatch): JsonResponse
    {
        $rows = SapStagingRow::where('import_batch_id', $importBatch->id)
            ->with('mappedAccount')
            ->orderBy('row_number')
            ->limit(500)
            ->get();

        $summary = [
            'total' => $importBatch->total_rows,
            'mapped' => $rows->where('mapping_status', 'mapped')->count(),
            'unmapped' => $rows->where('mapping_status', 'unmapped')->count(),
            'ambiguous' => $rows->where('mapping_status', 'ambiguous')->count(),
            'debit_sum' => $rows->sum('raw_debit'),
            'credit_sum' => $rows->sum('raw_credit'),
        ];

        return response()->json([
            'batch' => $importBatch,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
