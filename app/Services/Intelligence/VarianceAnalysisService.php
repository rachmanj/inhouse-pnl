<?php

namespace App\Services\Intelligence;

use App\Models\PnlLine;
use App\Models\PnlSnapshot;
use App\Models\PnlSnapshotLine;
use App\Models\ReportPeriod;
use App\Models\VarianceFlag;
use App\Repositories\DailyProductionRepository;
use Illuminate\Support\Collection;

class VarianceAnalysisService
{
    public function __construct(
        private DailyProductionRepository $dailyProduction,
    ) {}

    public function analyze(PnlSnapshot $snapshot): Collection
    {
        $snapshot->load(['reportPeriod', 'projectSite', 'lines']);
        $period = $snapshot->reportPeriod;
        $site = $snapshot->projectSite;

        if (! $period || ! $site) {
            return collect();
        }

        VarianceFlag::where('report_period_id', $period->id)
            ->where('project_site_id', $site->id)
            ->delete();

        $flags = collect();
        $baselineSnapshot = $this->baselineSnapshot($period, $site->id);
        $priorSnapshot = $this->priorMonthSnapshot($period, $site->id);

        foreach ($snapshot->lines as $line) {
            foreach (['yoy' => $baselineSnapshot, 'mom' => $priorSnapshot, 'budget' => null] as $type => $compareSnapshot) {
                $flag = match ($type) {
                    'yoy' => $this->compareSnapshots($period, $site->id, $line, $compareSnapshot, 'yoy'),
                    'mom' => $this->compareSnapshots($period, $site->id, $line, $compareSnapshot, 'mom'),
                    'budget' => $this->compareBudget($period, $site, $line),
                };

                if ($flag) {
                    $flags->push($flag);
                }
            }
        }

        return $flags;
    }

    private function compareSnapshots(ReportPeriod $period, int $siteId, PnlSnapshotLine $line, ?PnlSnapshot $compare, string $type): ?VarianceFlag
    {
        if (! $compare) {
            return null;
        }

        $compareLine = $compare->lines->firstWhere('pnl_line_id', $line->pnl_line_id);
        $current = (float) $line->amount;
        $baseline = (float) ($compareLine?->amount ?? 0);
        $delta = $current - $baseline;
        $percent = $baseline != 0.0 ? ($delta / abs($baseline)) * 100 : ($current != 0.0 ? 100.0 : 0.0);

        return $this->maybeCreateFlag($period, $siteId, $line->pnl_line_id, $type, $delta, $percent, abs($current));
    }

    private function compareBudget(ReportPeriod $period, $site, PnlSnapshotLine $line): ?VarianceFlag
    {
        $pnlLine = PnlLine::find($line->pnl_line_id);
        if (! $pnlLine) {
            return null;
        }

        $budget = $this->dailyProduction->budgetTarget($site->code, $period->year, $period->month, $pnlLine->code);
        if ($budget === null) {
            return null;
        }

        $current = (float) $line->amount;
        $delta = $current - $budget;
        $percent = $budget != 0.0 ? ($delta / abs($budget)) * 100 : 0.0;

        return $this->maybeCreateFlag($period, $site->id, $line->pnl_line_id, 'budget', $delta, $percent, abs($current));
    }

    private function maybeCreateFlag(ReportPeriod $period, int $siteId, int $pnlLineId, string $type, float $delta, float $percent, float $magnitude): ?VarianceFlag
    {
        $threshold = $this->thresholdFor($magnitude);
        if (abs($percent) < $threshold) {
            return null;
        }

        $severity = match (true) {
            abs($percent) >= config('intelligence.variance.critical_percent_threshold') => 'critical',
            abs($percent) >= config('intelligence.variance.warning_percent_threshold') => 'warning',
            default => 'info',
        };

        return VarianceFlag::create([
            'report_period_id' => $period->id,
            'project_site_id' => $siteId,
            'pnl_line_id' => $pnlLineId,
            'comparison_type' => $type,
            'delta_absolute' => round($delta, 2),
            'delta_percent' => round($percent, 2),
            'severity' => $severity,
        ]);
    }

    private function thresholdFor(float $magnitude): float
    {
        if ($magnitude >= config('intelligence.variance.large_account_threshold')) {
            return config('intelligence.variance.large_account_percent_threshold');
        }

        return config('intelligence.variance.default_percent_threshold');
    }

    private function baselineSnapshot(ReportPeriod $period, int $siteId): ?PnlSnapshot
    {
        $baselinePeriod = ReportPeriod::where('year', $period->baseline_year)
            ->where('month', $period->month)
            ->first();

        if (! $baselinePeriod) {
            return null;
        }

        return PnlSnapshot::where('report_period_id', $baselinePeriod->id)
            ->where('project_site_id', $siteId)
            ->with('lines')
            ->first();
    }

    private function priorMonthSnapshot(ReportPeriod $period, int $siteId): ?PnlSnapshot
    {
        $priorMonth = $period->month === 1 ? 12 : $period->month - 1;
        $priorYear = $period->month === 1 ? $period->year - 1 : $period->year;

        $priorPeriod = ReportPeriod::where('year', $priorYear)
            ->where('month', $priorMonth)
            ->first();

        if (! $priorPeriod) {
            return null;
        }

        return PnlSnapshot::where('report_period_id', $priorPeriod->id)
            ->where('project_site_id', $siteId)
            ->with('lines')
            ->first();
    }
}
