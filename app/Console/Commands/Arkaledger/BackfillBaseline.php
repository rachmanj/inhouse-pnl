<?php

namespace App\Console\Commands\Arkaledger;

use App\Enums\ReportPeriodStatus;
use App\Models\ImportBatch;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Periods\PeriodStateService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use App\Jobs\StageImportBatchJob;
use App\Jobs\MapAndValidateImportBatchJob;
use App\Jobs\UpsertAccountBalancesJob;
use App\Models\User;

#[Signature('arkaledger:backfill-baseline {--year=2024} {--path=}')]
#[Description('Backfill baseline year data from historical SAP Excel exports')]
class BackfillBaseline extends Command
{
    public function handle(PeriodStateService $periodState): int
    {
        $year = (int) $this->option('year');
        $path = $this->option('path');

        if (! $path || ! is_dir($path)) {
            $this->error('--path must point to a directory of per-site Excel exports.');

            return self::FAILURE;
        }

        $actor = User::role('Super Admin')->first() ?? User::first();
        if (! $actor) {
            $this->error('No user found to attribute period locks.');

            return self::FAILURE;
        }

        $files = glob(rtrim($path, '/').'/*.{xlsx,xls}', GLOB_BRACE) ?: [];

        foreach ($files as $file) {
            $siteCode = pathinfo($file, PATHINFO_FILENAME);
            $site = ProjectSite::where('code', $siteCode)->first();

            if (! $site) {
                $this->warn("Skipping unknown site file: {$file}");
                continue;
            }

            for ($month = 1; $month <= 12; $month++) {
                $period = ReportPeriod::firstOrCreate(
                    ['year' => $year, 'month' => $month],
                    ['status' => 'open', 'baseline_year' => $year]
                );

                $batch = ImportBatch::create([
                    'report_period_id' => $period->id,
                    'project_site_id' => $site->id,
                    'source' => 'upload',
                    'status' => 'pending',
                    'original_filename' => basename($file),
                    'file_path' => $file,
                    'triggered_by' => $actor->id,
                    'started_at' => now(),
                ]);

                Bus::chain([
                    new StageImportBatchJob($batch),
                    new MapAndValidateImportBatchJob($batch),
                    new UpsertAccountBalancesJob($batch),
                ])->dispatch();

                $this->info("Queued import for {$siteCode} {$year}-".str_pad((string) $month, 2, '0', STR_PAD_LEFT));

                if ($period->status !== 'locked') {
                    try {
                        if ($period->status === 'open') {
                            $periodState->transition($period, ReportPeriodStatus::InReview, $actor);
                            $period->refresh();
                        }
                        if ($period->status === 'in_review') {
                            $periodState->transition($period, ReportPeriodStatus::Approved, $actor);
                            $period->refresh();
                        }
                        if ($period->status === 'approved') {
                            $periodState->transition($period, ReportPeriodStatus::Delivered, $actor);
                            $period->refresh();
                        }
                        $periodState->transition($period, ReportPeriodStatus::Locked, $actor);
                    } catch (\Throwable $e) {
                        $this->warn("Could not lock period {$year}-{$month}: ".$e->getMessage());
                    }
                }
            }
        }

        $this->info('Baseline backfill jobs dispatched.');

        return self::SUCCESS;
    }
}
