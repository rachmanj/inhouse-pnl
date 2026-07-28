<?php

namespace App\Services\Reports\Sheets;

use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Models\TaxFiling;
use App\Services\Reports\SheetDefinition;

class MonthlyTaxReportSheetBuilder implements SheetBuilderInterface
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition
    {
        $filings = TaxFiling::with('projectSite')
            ->where('report_period_id', $period->id)
            ->when($site, fn ($q) => $q->where('project_site_id', $site->id))
            ->get()
            ->groupBy('tax_type');

        $rows = [];
        foreach ($filings as $taxType => $group) {
            $rows[] = [
                strtoupper($taxType),
                $group->count(),
                (float) $group->sum('amount_reported'),
                $group->where('status', 'filed')->count(),
                $group->where('status', 'late')->count(),
                $group->where('status', 'pending')->count(),
            ];
        }

        return new SheetDefinition(
            name: 'MONTHLY TAX REPORT',
            headerRows: [['MONTHLY TAX REPORT — '.$period->year.'-'.str_pad((string) $period->month, 2, '0', STR_PAD_LEFT)]],
            columnGroups: [
                ['title' => 'Summary', 'columns' => ['Tax Type', 'Filings', 'Total Reported', 'Filed', 'Late', 'Pending']],
            ],
            dataRows: $rows,
        );
    }
}
