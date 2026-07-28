<?php

namespace App\Jobs;

use App\Models\AccountBalance;
use App\Models\ImportBatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class UpsertAccountBalancesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ImportBatch $importBatch) {}

    public function handle(): void
    {
        $batch = $this->importBatch->fresh()->load('stagingRows');

        $unresolved = $batch->stagingRows()
            ->whereIn('mapping_status', ['unmapped', 'ambiguous'])
            ->count();

        if ($unresolved > 0) {
            return;
        }

        try {
            $source = match ($batch->source) {
                'email' => 'email',
                'sap_scheduled', 'service_layer' => 'sap',
                default => 'upload',
            };

            foreach ($batch->stagingRows as $row) {
                if ($row->mapping_status !== 'mapped' || ! $row->mapped_account_id) {
                    continue;
                }

                $debit = (float) ($row->raw_debit ?? 0);
                $credit = (float) ($row->raw_credit ?? 0);
                $balance = $row->raw_balance !== null
                    ? (float) $row->raw_balance
                    : $debit - $credit;

                AccountBalance::updateOrCreate(
                    [
                        'report_period_id' => $batch->report_period_id,
                        'project_site_id' => $batch->project_site_id,
                        'account_id' => $row->mapped_account_id,
                        'source' => $source,
                    ],
                    [
                        'debit' => $debit,
                        'credit' => $credit,
                        'balance' => $balance,
                        'import_batch_id' => $batch->id,
                    ]
                );
            }

            $batch->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            if ($batch->project_site_id) {
                RecalculatePnlSnapshotJob::dispatch(
                    $batch->reportPeriod,
                    $batch->projectSite
                );
            }
        } catch (Throwable $e) {
            $batch->update([
                'status' => 'failed',
                'error_summary' => ['message' => $e->getMessage()],
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
