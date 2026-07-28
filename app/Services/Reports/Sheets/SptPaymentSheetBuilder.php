<?php

namespace App\Services\Reports\Sheets;

use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Models\TaxFiling;
use App\Models\TaxPayment;
use App\Services\Reports\SheetDefinition;

class SptPaymentSheetBuilder implements SheetBuilderInterface
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition
    {
        $filings = TaxFiling::with(['projectSite', 'payments'])
            ->where('report_period_id', $period->id)
            ->when($site, fn ($q) => $q->where('project_site_id', $site->id))
            ->orderBy('due_date')
            ->cursor();

        $rows = [];
        foreach ($filings as $filing) {
            foreach ($filing->payments as $payment) {
                $rows[] = [
                    $filing->projectSite?->code ?? 'ENTITY',
                    $filing->tax_type,
                    $filing->filing_number,
                    $filing->due_date?->format('Y-m-d'),
                    $filing->status,
                    (float) $filing->amount_reported,
                    $payment->payment_date?->format('Y-m-d'),
                    (float) $payment->amount,
                    $payment->payment_reference,
                ];
            }

            if ($filing->payments->isEmpty()) {
                $rows[] = [
                    $filing->projectSite?->code ?? 'ENTITY',
                    $filing->tax_type,
                    $filing->filing_number,
                    $filing->due_date?->format('Y-m-d'),
                    $filing->status,
                    (float) $filing->amount_reported,
                    '',
                    0,
                    '',
                ];
            }
        }

        return new SheetDefinition(
            name: 'SPT & PAYMENT',
            headerRows: [['SPT & PAYMENT — Historical']],
            columnGroups: [
                ['title' => 'SPT', 'columns' => ['Site', 'Tax Type', 'Filing No', 'Due Date', 'Status', 'Amount Reported', 'Payment Date', 'Payment Amount', 'Reference']],
            ],
            dataRows: $rows,
            engine: 'streaming',
        );
    }
}
