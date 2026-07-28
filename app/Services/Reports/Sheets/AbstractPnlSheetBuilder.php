<?php

namespace App\Services\Reports\Sheets;

use App\Models\PnlLine;
use App\Models\PnlSnapshot;
use App\Models\PnlSnapshotLine;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Reports\SheetDefinition;

abstract class AbstractPnlSheetBuilder implements SheetBuilderInterface
{
    protected function pnlColumnGroups(ReportPeriod $period): array
    {
        $baselineYear = $period->baseline_year;
        $currentYear = $period->year;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return [
            ['title' => 'Account', 'columns' => ['Description']],
            ['title' => "{$baselineYear} (baseline)", 'columns' => array_merge($months, ['TOTAL', 'AVG', '%'])],
            ['title' => "{$currentYear} (current)", 'columns' => array_merge($months, ['TOTAL', 'AVG', '%'])],
        ];
    }

    protected function snapshotFor(ReportPeriod $period, ProjectSite $site): ?PnlSnapshot
    {
        return PnlSnapshot::where('report_period_id', $period->id)
            ->where('project_site_id', $site->id)
            ->with('lines')
            ->first();
    }

    protected function baselineSnapshotFor(ReportPeriod $period, ProjectSite $site): ?PnlSnapshot
    {
        $baselinePeriod = ReportPeriod::where('year', $period->baseline_year)
            ->where('month', $period->month)
            ->first();

        if (! $baselinePeriod) {
            return null;
        }

        return PnlSnapshot::where('report_period_id', $baselinePeriod->id)
            ->where('project_site_id', $site->id)
            ->with('lines')
            ->first();
    }

    protected function buildPnlRow(PnlLine $line, ?PnlSnapshot $baseline, ?PnlSnapshot $current): array
    {
        $baselineAmounts = $this->monthlyValues($baseline, $line->id);
        $currentAmounts = $this->monthlyValues($current, $line->id);

        return array_merge(
            [$line->name],
            $baselineAmounts,
            $currentAmounts,
        );
    }

    protected function monthlyValues(?PnlSnapshot $snapshot, int $pnlLineId): array
    {
        $amounts = array_fill(0, 12, 0.0);

        if ($snapshot) {
            $lines = $snapshot->lines->where('pnl_line_id', $pnlLineId);
            foreach ($lines as $line) {
                $amounts[$line->month - 1] = (float) $line->amount;
            }
        }

        $total = array_sum($amounts);
        $avg = $total / 12;
        $revenue = $this->revenueTotal($snapshot);
        $percent = $revenue != 0.0 ? ($total / $revenue) * 100 : 0.0;

        return array_merge(
            array_map(fn ($v) => round($v, 2), $amounts),
            [round($total, 2), round($avg, 2), round($percent, 2)]
        );
    }

    protected function revenueTotal(?PnlSnapshot $snapshot): float
    {
        if (! $snapshot) {
            return 0.0;
        }

        $revenueLine = PnlLine::where('code', 'REVENUE')->first();
        if (! $revenueLine) {
            return 0.0;
        }

        return (float) $snapshot->lines
            ->where('pnl_line_id', $revenueLine->id)
            ->sum('amount');
    }

    protected function sheetName(string $prefix, ?ProjectSite $site): string
    {
        return $site ? "{$prefix} {$site->code}" : $prefix;
    }
}
