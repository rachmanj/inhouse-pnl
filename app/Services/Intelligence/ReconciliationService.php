<?php

namespace App\Services\Intelligence;

use App\Jobs\NotifyInsightJob;
use App\Models\AccountBalance;
use App\Models\ImportBatch;
use App\Models\ReconciliationCheck;
use Illuminate\Support\Facades\DB;

class ReconciliationService
{
    public function check(ImportBatch $batch, ?float $sapControlTotal = null): ReconciliationCheck
    {
        $systemTotal = (float) AccountBalance::where('import_batch_id', $batch->id)->sum('balance');
        $controlTotal = $sapControlTotal ?? $systemTotal;
        $discrepancy = $systemTotal - $controlTotal;
        $tolerance = config('intelligence.reconciliation.tolerance');
        $isReconciled = abs($discrepancy) <= $tolerance;

        $detail = AccountBalance::where('import_batch_id', $batch->id)
            ->with('account')
            ->get()
            ->map(fn ($b) => [
                'account' => $b->account?->sap_code,
                'balance' => (float) $b->balance,
            ])
            ->toArray();

        $check = ReconciliationCheck::updateOrCreate(
            [
                'import_batch_id' => $batch->id,
                'checkable_type' => ImportBatch::class,
                'checkable_id' => $batch->id,
            ],
            [
                'sap_control_total' => $controlTotal,
                'system_total' => $systemTotal,
                'discrepancy' => $discrepancy,
                'is_reconciled' => $isReconciled,
                'discrepancy_detail' => $detail,
            ]
        );

        if (! $isReconciled) {
            NotifyInsightJob::dispatch(
                'reconciliation_failed',
                "Import batch #{$batch->id} failed reconciliation: discrepancy ".number_format($discrepancy, 2)
            );
        }

        return $check;
    }
}
