<?php

namespace App\Console\Commands\Arkaledger;

use App\Models\ReportPeriod;
use App\Models\TaxFiling;
use App\Models\TaxPayment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OpenSpout\Reader\XLSX\Reader;

#[Signature('arkaledger:import-spt-history {--from=2017} {--to=2026} {--path=}')]
#[Description('Import historical SPT & payment rows from a large Excel file')]
class ImportSptHistory extends Command
{
    public function handle(): int
    {
        $path = $this->option('path');
        if (! $path || ! file_exists($path)) {
            $this->error('--path must point to an existing SPT history Excel file.');

            return self::FAILURE;
        }

        $from = (int) $this->option('from');
        $to = (int) $this->option('to');
        $reader = new Reader;
        $reader->open($path);

        $buffer = [];
        $count = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            $isHeader = true;
            foreach ($sheet->getRowIterator() as $row) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $cells = $row->toArray();
                if (count($cells) < 6) {
                    continue;
                }

                $year = (int) ($cells[0] ?? 0);
                if ($year < $from || $year > $to) {
                    continue;
                }

                $month = (int) ($cells[1] ?? 1);
                $period = ReportPeriod::firstOrCreate(
                    ['year' => $year, 'month' => $month],
                    ['status' => 'locked', 'baseline_year' => 2024]
                );

                $filing = TaxFiling::create([
                    'report_period_id' => $period->id,
                    'tax_type' => $cells[2] ?? 'ppn',
                    'filing_number' => $cells[3] ?? null,
                    'due_date' => $cells[4] ?? now()->toDateString(),
                    'amount_reported' => (float) ($cells[5] ?? 0),
                    'status' => 'filed',
                    'source' => 'manual',
                ]);

                if (! empty($cells[6]) && ! empty($cells[7])) {
                    TaxPayment::create([
                        'tax_filing_id' => $filing->id,
                        'payment_date' => $cells[6],
                        'amount' => (float) $cells[7],
                        'payment_reference' => $cells[8] ?? null,
                    ]);
                }

                $count++;
                if ($count % 1000 === 0) {
                    $this->info("Imported {$count} rows...");
                }
            }
        }

        $reader->close();
        $this->info("SPT history import complete: {$count} rows.");

        return self::SUCCESS;
    }
}
