<?php

namespace App\Services\Reports\Sheets;

use App\Models\Journal;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Reports\SheetDefinition;

class JournalEntrySheetBuilder implements SheetBuilderInterface
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition
    {
        $journals = Journal::with(['lines.account', 'projectSite'])
            ->where('report_period_id', $period->id)
            ->when($site, fn ($q) => $q->where('project_site_id', $site->id))
            ->orderBy('reference_no')
            ->get();

        $rows = [];
        foreach ($journals as $journal) {
            foreach ($journal->lines as $line) {
                $rows[] = [
                    $journal->reference_no,
                    $journal->projectSite?->code,
                    $journal->description,
                    $line->account?->sap_code,
                    $line->account?->name,
                    (float) $line->debit,
                    (float) $line->credit,
                    $line->memo,
                ];
            }
        }

        return new SheetDefinition(
            name: 'JOURNAL ENTRY',
            headerRows: [['JOURNAL ENTRY — '.$period->year.'-'.str_pad((string) $period->month, 2, '0', STR_PAD_LEFT)]],
            columnGroups: [
                ['title' => 'Journal', 'columns' => ['Reference', 'Site', 'Description', 'Account', 'Account Name', 'Debit', 'Credit', 'Memo']],
            ],
            dataRows: $rows,
        );
    }
}
