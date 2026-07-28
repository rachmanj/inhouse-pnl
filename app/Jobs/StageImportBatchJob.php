<?php

namespace App\Jobs;

use App\Models\ImportBatch;
use App\Models\SapStagingRow;
use App\Services\Import\SapExcelParserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StageImportBatchJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ImportBatch $importBatch) {}

    public function handle(SapExcelParserService $parser): void
    {
        $batch = $this->importBatch->fresh();

        $batch->update([
            'status' => 'pending',
            'started_at' => now(),
        ]);

        try {
            $filePath = Storage::disk('local')->path($batch->file_path);
            $layout = $parser->detectLayout($filePath);
            $columnMap = $parser->guessColumnMap($layout);
            $layout->columnMap = $columnMap;

            $rows = $parser->extractRows($filePath, $layout);

            $batch->stagingRows()->delete();

            foreach ($rows as $row) {
                SapStagingRow::create([
                    'import_batch_id' => $batch->id,
                    ...$row,
                ]);
            }

            $batch->update([
                'status' => 'staged',
                'total_rows' => $rows->count(),
                'staged_rows' => $rows->count(),
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
}
