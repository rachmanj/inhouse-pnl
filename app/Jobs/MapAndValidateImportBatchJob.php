<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\ImportBatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Throwable;

class MapAndValidateImportBatchJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ImportBatch $importBatch) {}

    public function handle(): void
    {
        $batch = $this->importBatch->fresh();

        if ($batch->status === 'failed') {
            return;
        }

        try {
            $accountsByCode = Account::pluck('id', 'sap_code');
            $normalizedAccounts = Account::all()->mapWithKeys(fn ($a) => [
                $this->normalizeCode($a->sap_code) => $a->id,
            ]);

            $mapped = 0;
            $errors = 0;

            foreach ($batch->stagingRows as $row) {
                $code = trim((string) $row->raw_account_code);

                if ($code === '') {
                    $row->update([
                        'mapping_status' => 'error',
                        'error_message' => 'Empty account code',
                    ]);
                    $errors++;

                    continue;
                }

                if ($accountsByCode->has($code)) {
                    $row->update([
                        'mapped_account_id' => $accountsByCode[$code],
                        'mapping_status' => 'mapped',
                        'error_message' => null,
                    ]);
                    $mapped++;

                    continue;
                }

                $normalized = $this->normalizeCode($code);
                $matches = $normalizedAccounts->filter(fn ($id, $key) => $key === $normalized
                    || Str::startsWith($key, $normalized)
                    || Str::startsWith($normalized, $key));

                if ($matches->count() === 1) {
                    $row->update([
                        'mapped_account_id' => $matches->first(),
                        'mapping_status' => 'mapped',
                        'error_message' => null,
                    ]);
                    $mapped++;

                    continue;
                }

                if ($matches->count() > 1) {
                    $row->update([
                        'mapping_status' => 'ambiguous',
                        'error_message' => 'Multiple account matches found',
                    ]);
                    $errors++;

                    continue;
                }

                $row->update([
                    'mapping_status' => 'unmapped',
                    'error_message' => 'No matching account found',
                ]);
                $errors++;
            }

            $status = $errors > 0 ? 'mapped' : 'validated';

            $batch->update([
                'status' => $status,
                'mapped_rows' => $mapped,
                'error_rows' => $errors,
            ]);
        } catch (Throwable $e) {
            $batch->update([
                'status' => 'failed',
                'error_summary' => ['message' => $e->getMessage()],
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    private function normalizeCode(string $code): string
    {
        return Str::upper(preg_replace('/[^A-Za-z0-9]/', '', $code));
    }
}
