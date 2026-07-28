<?php

namespace App\Services\Reports;

use App\Models\ReportArtifact;
use App\Models\ReportPackage;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportRenderer
{
    public function render(ReportPackage $package): ReportArtifact
    {
        $period = $package->reportPeriod;
        $path = storage_path('app/reports/package_'.$package->id.'_'.time().'.pdf');
        @mkdir(dirname($path), 0755, true);

        Pdf::loadView('reports.pdf.monthly-report', [
            'package' => $package,
            'period' => $period,
        ])->save($path);

        return ReportArtifact::create([
            'report_package_id' => $package->id,
            'type' => 'pdf',
            'file_path' => $path,
            'file_hash' => hash_file('sha256', $path),
            'generated_at' => now(),
        ]);
    }
}
