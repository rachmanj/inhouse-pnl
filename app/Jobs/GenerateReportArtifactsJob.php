<?php

namespace App\Jobs;

use App\Models\ReportPackage;
use App\Services\Reports\PdfReportRenderer;
use App\Services\Reports\WorkbookGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReportArtifactsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ReportPackage $package,
    ) {}

    public function handle(WorkbookGeneratorService $workbook, PdfReportRenderer $pdf): void
    {
        $this->package->load('reportPeriod');

        $workbook->generate($this->package);
        $pdf->render($this->package);
    }
}
