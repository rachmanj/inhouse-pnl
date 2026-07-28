<?php

namespace App\Actions\Import;

use App\Jobs\UpsertAccountBalancesJob;
use App\Models\ImportBatch;
use App\Models\SapStagingRow;

class ResolveAmbiguousMappingAction
{
    public function execute(SapStagingRow $row, int $accountId): SapStagingRow
    {
        $row->update([
            'mapped_account_id' => $accountId,
            'mapping_status' => 'mapped',
            'error_message' => null,
        ]);

        $batch = $row->importBatch;

        $batch->update([
            'mapped_rows' => $batch->stagingRows()->where('mapping_status', 'mapped')->count(),
            'error_rows' => $batch->stagingRows()->whereIn('mapping_status', ['unmapped', 'ambiguous', 'error'])->count(),
        ]);

        $unresolved = $batch->stagingRows()
            ->whereIn('mapping_status', ['unmapped', 'ambiguous'])
            ->count();

        if ($unresolved === 0 && $batch->status !== 'completed') {
            $batch->update(['status' => 'validated']);
        }

        return $row->fresh();
    }

    public function confirmBatch(ImportBatch $batch): void
    {
        UpsertAccountBalancesJob::dispatch($batch);
    }
}
