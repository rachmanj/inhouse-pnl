<?php

namespace App\Services\Reports\Sheets;

use App\Models\PettyCashFund;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Reports\SheetDefinition;

class PettyCashSummarySheetBuilder implements SheetBuilderInterface
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition
    {
        $funds = PettyCashFund::with(['projectSite', 'expenses'])
            ->where('report_period_id', $period->id)
            ->when($site, fn ($q) => $q->where('project_site_id', $site->id))
            ->get();

        $rows = [];
        foreach ($funds as $fund) {
            $expenseTotal = $fund->expenses->sum('amount');
            $rows[] = [
                $fund->projectSite?->code,
                (float) $fund->opening_balance,
                (float) $fund->replenishment_amount,
                (float) $expenseTotal,
                (float) $fund->closing_balance,
                $fund->status,
            ];
        }

        return new SheetDefinition(
            name: 'PETTY CASH SUMMARY',
            headerRows: [['PETTY CASH SUMMARY — '.$period->year.'-'.str_pad((string) $period->month, 2, '0', STR_PAD_LEFT)]],
            columnGroups: [
                ['title' => 'Fund', 'columns' => ['Site', 'Opening', 'Replenishment', 'Expenses', 'Closing', 'Status']],
            ],
            dataRows: $rows,
        );
    }
}
