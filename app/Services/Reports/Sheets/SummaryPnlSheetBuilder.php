<?php

namespace App\Services\Reports\Sheets;

use App\Models\PnlLine;
use App\Models\PnlSnapshot;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Reports\SheetDefinition;

class SummaryPnlSheetBuilder extends AbstractPnlSheetBuilder
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition
    {
        $lines = PnlLine::where(function ($q) {
            $q->where('is_subtotal', true)
                ->orWhereIn('code', ['REVENUE_ENGINEERING', 'PROFIT_LOSS']);
        })
            ->orderBy('sort_order')
            ->get();

        $baseline = PnlSnapshot::where('report_period_id', $period->id)
            ->whereNull('project_site_id')
            ->with('lines')
            ->first();

        $baselinePeriod = ReportPeriod::where('year', $period->baseline_year)
            ->where('month', $period->month)
            ->first();

        if ($baselinePeriod) {
            $baseline = PnlSnapshot::where('report_period_id', $baselinePeriod->id)
                ->whereNull('project_site_id')
                ->with('lines')
                ->first();
        }

        $current = PnlSnapshot::where('report_period_id', $period->id)
            ->whereNull('project_site_id')
            ->with('lines')
            ->first();

        $rows = [];
        foreach ($lines as $line) {
            $rows[] = $this->buildPnlRow($line, $baseline, $current);
        }

        return new SheetDefinition(
            name: 'SUMMARY P&L',
            headerRows: [['SUMMARY P&L — Consolidated '.$period->year]],
            columnGroups: $this->pnlColumnGroups($period),
            dataRows: $rows,
        );
    }
}
