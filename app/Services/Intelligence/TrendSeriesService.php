<?php

namespace App\Services\Intelligence;

use App\Models\PnlSnapshotLine;
use App\Models\ReportPeriod;

class TrendSeriesService
{
    public function buildSeries(int $pnlLineId, ?int $siteId, int $months = 24): array
    {
        $periods = ReportPeriod::orderByDesc('year')
            ->orderByDesc('month')
            ->limit($months)
            ->get()
            ->reverse()
            ->values();

        $series = [];

        foreach ($periods as $period) {
            $line = PnlSnapshotLine::whereHas('snapshot', function ($q) use ($period, $siteId) {
                $q->where('report_period_id', $period->id)
                    ->where('project_site_id', $siteId);
            })
                ->where('pnl_line_id', $pnlLineId)
                ->where('year', $period->year)
                ->where('month', $period->month)
                ->first();

            $series[] = [
                'period' => sprintf('%04d-%02d', $period->year, $period->month),
                'amount' => (float) ($line?->amount ?? 0),
                'baseline_year' => $period->baseline_year,
            ];
        }

        return $series;
    }
}
