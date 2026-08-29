<?php

namespace App\Services\Sap;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\CoaMapping;
use App\Models\ImportBatch;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Models\SapSyncRun;
use Illuminate\Support\Facades\DB;

class SapAccountBalanceSyncService
{
    public function pull(ReportPeriod $period, string $triggeredBy = 'scheduler'): SapSyncRun
    {
        $run = SapSyncRun::create([
            'report_period_id' => $period->id,
            'status' => 'running',
            'triggered_by' => $triggeredBy,
            'started_at' => now(),
        ]);

        $created = 0;
        $updated = 0;
        $failed = 0;
        $errors = [];

        try {
            $rows = DB::connection('sap')
                ->table('JDT1')
                ->join('OACT', 'JDT1.Account', '=', 'OACT.AcctCode')
                ->whereYear('JDT1.RefDate', $period->year)
                ->whereMonth('JDT1.RefDate', $period->month)
                ->select([
                    'OACT.AcctCode as sap_code',
                    'JDT1.ProfitCode as cost_center',
                    DB::raw('SUM(JDT1.Debit) as debit'),
                    DB::raw('SUM(JDT1.Credit) as credit'),
                ])
                ->groupBy('OACT.AcctCode', 'JDT1.ProfitCode')
                ->get();

            $batch = ImportBatch::create([
                'report_period_id' => $period->id,
                'source' => 'sap_scheduled',
                'status' => 'completed',
                'original_filename' => 'sap-sync-'.$period->year.'-'.$period->month,
                'started_at' => now(),
                'completed_at' => now(),
            ]);

            $accounts = Account::pluck('id', 'sap_code');
            $sites = ProjectSite::pluck('id', 'code');
            $mappings = CoaMapping::pluck('account_id', 'account_id');

            foreach ($rows as $row) {
                try {
                    $accountId = $accounts[$row->sap_code] ?? null;
                    $siteId = $sites[$row->cost_center] ?? null;

                    if (! $accountId || ! $siteId) {
                        $failed++;
                        continue;
                    }

                    $balance = (float) $row->debit - (float) $row->credit;
                    $existing = AccountBalance::where([
                        'report_period_id' => $period->id,
                        'project_site_id' => $siteId,
                        'account_id' => $accountId,
                        'source' => 'sap',
                    ])->first();

                    AccountBalance::updateOrCreate(
                        [
                            'report_period_id' => $period->id,
                            'project_site_id' => $siteId,
                            'account_id' => $accountId,
                            'source' => 'sap',
                        ],
                        [
                            'debit' => $row->debit,
                            'credit' => $row->credit,
                            'balance' => $balance,
                            'import_batch_id' => $batch->id,
                        ]
                    );

                    $existing ? $updated++ : $created++;
                } catch (\Throwable $e) {
                    $failed++;
                    $errors[] = $e->getMessage();
                }
            }

            $run->update([
                'status' => 'completed',
                'created_count' => $created,
                'updated_count' => $updated,
                'failed_count' => $failed,
                'error_summary' => $errors ? implode("\n", array_slice($errors, 0, 10)) : null,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $run->update([
                'status' => 'failed',
                'failed_count' => $failed + 1,
                'error_summary' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }

        return $run->fresh();
    }
}
