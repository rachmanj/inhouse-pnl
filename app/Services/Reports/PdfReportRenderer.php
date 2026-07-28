<?php

namespace App\Services\Reports;

use App\Models\PnlSnapshot;
use App\Models\ProjectSite;
use App\Models\ReportArtifact;
use App\Models\ReportPackage;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportRenderer
{
    public function render(ReportPackage $package): ReportArtifact
    {
        $period = $package->reportPeriod;
        $snapshot = PnlSnapshot::where('report_period_id', $period->id)
            ->whereNull('project_site_id')
            ->with(['lines.pnlLine'])
            ->first();

        $siteHighlights = ProjectSite::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (ProjectSite $site) use ($period) {
                $siteSnapshot = PnlSnapshot::where('report_period_id', $period->id)
                    ->where('project_site_id', $site->id)
                    ->with('lines.pnlLine')
                    ->first();

                $profitLoss = $siteSnapshot?->lines
                    ->firstWhere('pnlLine.code', 'PROFIT_LOSS');

                return [
                    'code' => $site->code,
                    'name' => $site->name,
                    'profit_loss' => (float) ($profitLoss?->amount ?? 0),
                ];
            });

        $filename = sprintf(
            'reports/%d/monthly-report-%d-%02d-%s.pdf',
            $package->id,
            $period->year,
            $period->month,
            now()->format('YmdHis')
        );

        $fullPath = storage_path('app/'.$filename);
        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf = Pdf::loadView('reports.pdf.monthly-report', [
            'period' => $period,
            'snapshot' => $snapshot,
            'siteHighlights' => $siteHighlights,
            'generatedAt' => now(),
        ])->setPaper('a4');

        $pdf->save($fullPath);

        return ReportArtifact::create([
            'report_package_id' => $package->id,
            'type' => 'pdf',
            'file_path' => $filename,
            'file_hash' => hash_file('sha256', $fullPath),
            'generated_at' => now(),
        ]);
    }
}
