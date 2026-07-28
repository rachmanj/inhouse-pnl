<?php

namespace App\Services\Pnl;

use App\Models\PnlLine;
use App\Models\PnlSnapshot;
use App\Models\PnlSnapshotLine;

class SitePnlPresenter
{
    public function present(PnlSnapshot $baseline, PnlSnapshot $current, string $view = 'pnl'): array
    {
        $lines = PnlLine::with('children')
            ->whereNull('parent_id')
            ->where('code', '!=', 'ROOT')
            ->orderBy('sort_order')
            ->get();

        if ($view === 'pnl') {
            $lines = PnlLine::where('is_subtotal', true)
                ->orWhereIn('code', ['REVENUE_ENGINEERING', 'PROFIT_LOSS'])
                ->orderBy('sort_order')
                ->get();
        }

        $baselineLines = $baseline->lines->groupBy('pnl_line_id');
        $currentLines = $current->lines->groupBy('pnl_line_id');

        return [
            'rows' => $this->buildRows($lines, $baselineLines, $currentLines, $view),
            'baseline_year' => $baseline->reportPeriod->baseline_year ?? 2024,
            'current_year' => $current->reportPeriod->year,
        ];
    }

    private function buildRows($lines, $baselineLines, $currentLines, $view): array
    {
        $rows = [];

        foreach ($lines as $line) {
            $baselineAmounts = $this->monthlyAmounts($baselineLines->get($line->id));
            $currentAmounts = $this->monthlyAmounts($currentLines->get($line->id));

            $row = [
                'id' => $line->id,
                'code' => $line->code,
                'name' => $line->name,
                'is_subtotal' => $line->is_subtotal,
                'baseline' => $baselineAmounts,
                'current' => $currentAmounts,
                'children' => [],
            ];

            if ($view === 'rincian' && $line->children->isNotEmpty()) {
                $row['children'] = $this->buildRows($line->children, $baselineLines, $currentLines, $view);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function monthlyAmounts($lines): array
    {
        $months = array_fill(1, 12, 0);

        if (! $lines) {
            return ['months' => $months, 'total' => 0, 'avg' => 0, 'percent' => 0];
        }

        foreach ($lines as $line) {
            $months[$line->month] = (float) $line->amount;
        }

        $nonZero = array_filter($months);
        $total = array_sum($months);
        $avg = count($nonZero) > 0 ? $total / count($nonZero) : 0;

        return [
            'months' => $months,
            'total' => $total,
            'avg' => $avg,
            'percent' => 0,
        ];
    }
}
