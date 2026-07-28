<?php

namespace App\Services\Pnl;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\CoaMapping;
use App\Models\PnlLine;
use App\Models\PnlSnapshot;
use App\Models\PnlSnapshotLine;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use Illuminate\Support\Facades\DB;

class PnlAggregationService
{
    public function aggregateSite(ReportPeriod $period, ProjectSite $site): PnlSnapshot
    {
        $snapshot = PnlSnapshot::updateOrCreate(
            ['report_period_id' => $period->id, 'project_site_id' => $site->id],
            ['status' => 'draft', 'generated_at' => now()]
        );

        $balances = AccountBalance::where('report_period_id', $period->id)
            ->where('project_site_id', $site->id)
            ->get();

        $mappings = CoaMapping::with('pnlLine')
            ->where('effective_from', '<=', $period->year.'-'.str_pad($period->month, 2, '0', STR_PAD_LEFT).'-01')
            ->get()
            ->keyBy('account_id');

        $lineAmounts = [];

        foreach ($balances as $balance) {
            $mapping = $mappings->get($balance->account_id);
            if (! $mapping) {
                continue;
            }

            $lineId = $mapping->pnl_line_id;
            $lineAmounts[$lineId] = ($lineAmounts[$lineId] ?? 0) + (float) $balance->balance;
        }

        foreach ($lineAmounts as $lineId => $amount) {
            PnlSnapshotLine::updateOrCreate(
                [
                    'pnl_snapshot_id' => $snapshot->id,
                    'pnl_line_id' => $lineId,
                    'year' => $period->year,
                    'month' => $period->month,
                ],
                ['amount' => $amount]
            );
        }

        $this->rollUp($snapshot);

        return $snapshot->fresh();
    }

    public function aggregateConsolidated(ReportPeriod $period): PnlSnapshot
    {
        $snapshot = PnlSnapshot::updateOrCreate(
            ['report_period_id' => $period->id, 'project_site_id' => null],
            ['status' => 'draft', 'generated_at' => now()]
        );

        $siteSnapshots = PnlSnapshot::where('report_period_id', $period->id)
            ->whereNotNull('project_site_id')
            ->with('lines')
            ->get();

        $consolidated = [];

        foreach ($siteSnapshots as $siteSnapshot) {
            foreach ($siteSnapshot->lines as $line) {
                $key = $line->pnl_line_id.'_'.$line->year.'_'.$line->month;
                $consolidated[$key] = [
                    'pnl_line_id' => $line->pnl_line_id,
                    'year' => $line->year,
                    'month' => $line->month,
                    'amount' => ($consolidated[$key]['amount'] ?? 0) + (float) $line->amount,
                ];
            }
        }

        foreach ($consolidated as $data) {
            PnlSnapshotLine::updateOrCreate(
                [
                    'pnl_snapshot_id' => $snapshot->id,
                    'pnl_line_id' => $data['pnl_line_id'],
                    'year' => $data['year'],
                    'month' => $data['month'],
                ],
                ['amount' => $data['amount']]
            );
        }

        $this->rollUp($snapshot);

        return $snapshot->fresh();
    }

    public function rollUp(PnlSnapshot $snapshot): void
    {
        $lines = PnlLine::orderByDesc('sort_order')->get();

        foreach ($lines->where('is_subtotal', true) as $subtotalLine) {
            $children = PnlLine::where('parent_id', $subtotalLine->parent_id)
                ->where('id', '!=', $subtotalLine->id)
                ->pluck('id');

            $months = PnlSnapshotLine::where('pnl_snapshot_id', $snapshot->id)
                ->whereIn('pnl_line_id', $children)
                ->select('year', 'month')
                ->distinct()
                ->get();

            foreach ($months as $month) {
                $total = 0;
                foreach ($children as $childId) {
                    $childLine = PnlLine::find($childId);
                    $amount = PnlSnapshotLine::where('pnl_snapshot_id', $snapshot->id)
                        ->where('pnl_line_id', $childId)
                        ->where('year', $month->year)
                        ->where('month', $month->month)
                        ->value('amount') ?? 0;
                    $total += (float) $amount * $childLine->sign;
                }

                PnlSnapshotLine::updateOrCreate(
                    [
                        'pnl_snapshot_id' => $snapshot->id,
                        'pnl_line_id' => $subtotalLine->id,
                        'year' => $month->year,
                        'month' => $month->month,
                    ],
                    ['amount' => abs($total)]
                );
            }
        }
    }

    public function baselineVsCurrent(PnlSnapshot $current, int $baselineYear): array
    {
        $baselineSnapshot = PnlSnapshot::where('project_site_id', $current->project_site_id)
            ->whereHas('reportPeriod', fn ($q) => $q->where('year', $baselineYear))
            ->with('lines')
            ->first();

        return [
            'current' => $current->lines,
            'baseline' => $baselineSnapshot?->lines ?? collect(),
            'baseline_year' => $baselineYear,
        ];
    }
}
