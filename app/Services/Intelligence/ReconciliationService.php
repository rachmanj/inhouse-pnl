<?php

namespace App\Services\Intelligence;

use App\Models\ImportBatch;
use App\Models\ReconciliationCheck;

class ReconciliationService
{
    public function check(ImportBatch $batch): ReconciliationCheck
    {
        $systemTotal = $batch->stagingRows()->sum('raw_balance');

        return ReconciliationCheck::updateOrCreate(
            ['import_batch_id' => $batch->id],
            [
                'system_total' => $systemTotal,
                'discrepancy' => 0,
                'is_reconciled' => true,
            ]
        );
    }
}
