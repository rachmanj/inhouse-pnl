<?php

namespace App\Services\Reports\Sheets;

use App\Models\PnlLine;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Reports\SheetDefinition;

class PnlSheetBuilder extends AbstractPnlSheetBuilder
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition
    {
        $lines = PnlLine::where(function ($q) {
            $q->where('is_subtotal', true)
                ->orWhereIn('code', ['REVENUE_ENGINEERING', 'PROFIT_LOSS']);
        })
            ->orderBy('sort_order')
            ->get();

        $baseline = $site ? $this->baselineSnapshotFor($period, $site) : null;
        $current = $site ? $this->snapshotFor($period, $site) : null;

        $rows = [];
        foreach ($lines as $line) {
            $rows[] = $this->buildPnlRow($line, $baseline, $current);
        }

        return new SheetDefinition(
            name: $this->sheetName('P&L', $site),
            headerRows: [[$this->sheetName('P&L', $site).' — '.$period->year]],
            columnGroups: $this->pnlColumnGroups($period),
            dataRows: $rows,
        );
    }
}
