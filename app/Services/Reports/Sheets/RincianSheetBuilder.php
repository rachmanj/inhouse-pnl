<?php

namespace App\Services\Reports\Sheets;

use App\Models\PnlLine;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Reports\SheetDefinition;

class RincianSheetBuilder extends AbstractPnlSheetBuilder
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition
    {
        $lines = PnlLine::where('is_subtotal', false)
            ->whereDoesntHave('children')
            ->orderBy('sort_order')
            ->get();

        $baseline = $site ? $this->baselineSnapshotFor($period, $site) : null;
        $current = $site ? $this->snapshotFor($period, $site) : null;

        $rows = [];
        foreach ($lines as $line) {
            $rows[] = $this->buildPnlRow($line, $baseline, $current);
        }

        return new SheetDefinition(
            name: $this->sheetName('Rincian', $site),
            headerRows: [[$this->sheetName('Rincian', $site).' — '.$period->year]],
            columnGroups: $this->pnlColumnGroups($period),
            dataRows: $rows,
        );
    }

    public function buildHoJkt(ReportPeriod $period, ProjectSite $ho, ProjectSite $jkt): SheetDefinition
    {
        $lines = PnlLine::where('is_subtotal', false)
            ->whereDoesntHave('children')
            ->orderBy('sort_order')
            ->get();

        $name = 'Rincian HO & JKT';

        return new SheetDefinition(
            name: $name,
            headerRows: [[$name.' — '.$period->year]],
            columnGroups: $this->pnlColumnGroups($period),
            dataRows: $this->buildTwoSiteStackedRows($period, $ho, $jkt, $lines),
        );
    }
}
