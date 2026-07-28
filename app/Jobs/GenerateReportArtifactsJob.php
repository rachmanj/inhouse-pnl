<?php

namespace App\Jobs;

use App\Models\ReportArtifact;
use App\Models\ReportPackage;
use App\Services\Reports\PdfReportRenderer;
use App\Services\Reports\WorkbookGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReportArtifactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ReportPackage $reportPackage) {}

    public function handle(WorkbookGeneratorService $workbook, PdfReportRenderer $pdf): void
    {
        $excelPath = $workbook->generate($this->reportPackage);

        ReportArtifact::create([
            'report_package_id' => $this->reportPackage->id,
            'type' => 'excel',
            'file_path' => $excelPath,
            'file_hash' => hash_file('sha256', $excelPath),
            'generated_at' => now(),
        ]);

        $pdf->render($this->reportPackage);
    }
}
