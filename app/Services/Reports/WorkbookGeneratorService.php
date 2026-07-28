<?php

namespace App\Services\Reports;

use App\Models\ProjectSite;
use App\Models\ReportArtifact;
use App\Models\ReportPackage;
use App\Services\Reports\Sheets\JournalEntrySheetBuilder;
use App\Services\Reports\Sheets\MonthlyTaxReportSheetBuilder;
use App\Services\Reports\Sheets\PettyCashSummarySheetBuilder;
use App\Services\Reports\Sheets\PnlSheetBuilder;
use App\Services\Reports\Sheets\RincianSheetBuilder;
use App\Services\Reports\Sheets\SptPaymentSheetBuilder;
use App\Services\Reports\Sheets\SummaryPnlSheetBuilder;
use Illuminate\Support\Facades\Storage;

class WorkbookGeneratorService
{
    public function __construct(
        private PhpSpreadsheetExcelRenderer $styledRenderer,
        private OpenSpoutExcelRenderer $streamingRenderer,
        private JournalEntrySheetBuilder $journalEntrySheetBuilder,
        private PettyCashSummarySheetBuilder $pettyCashSummarySheetBuilder,
        private SptPaymentSheetBuilder $sptPaymentSheetBuilder,
        private MonthlyTaxReportSheetBuilder $monthlyTaxReportSheetBuilder,
        private RincianSheetBuilder $rincianSheetBuilder,
        private PnlSheetBuilder $pnlSheetBuilder,
        private SummaryPnlSheetBuilder $summaryPnlSheetBuilder,
    ) {}

    public function generate(ReportPackage $package): ReportArtifact
    {
        $period = $package->reportPeriod;
        $sites = ProjectSite::where('is_active', true)->orderBy('sort_order')->get()->keyBy('code');

        $definitions = $this->buildSheetDefinitions($period, $sites);

        $styledSheets = array_filter($definitions, fn (SheetDefinition $d) => $d->engine === 'styled');
        $streamingSheets = array_filter($definitions, fn (SheetDefinition $d) => $d->engine === 'streaming');

        $filename = sprintf(
            'reports/%d/monthly-report-%d-%02d-%s.xlsx',
            $package->id,
            $period->year,
            $period->month,
            now()->format('YmdHis')
        );

        $fullPath = storage_path('app/'.$filename);

        $this->styledRenderer->newWorkbook();
        foreach ($styledSheets as $definition) {
            $this->styledRenderer->addSheet($definition->name, $definition);
        }
        $this->styledRenderer->save($fullPath);

        if ($streamingSheets !== []) {
            $this->mergeStreamingSheets($fullPath, $streamingSheets);
        }

        $hash = hash_file('sha256', $fullPath);

        return ReportArtifact::create([
            'report_package_id' => $package->id,
            'type' => 'excel',
            'file_path' => $filename,
            'file_hash' => $hash,
            'generated_at' => now(),
        ]);
    }

    private function buildSheetDefinitions($period, $sites): array
    {
        $definitions = [];

        $definitions[] = $this->journalEntrySheetBuilder->build($period);
        $definitions[] = $this->pettyCashSummarySheetBuilder->build($period);
        $definitions[] = $this->sptPaymentSheetBuilder->build($period);
        $definitions[] = $this->monthlyTaxReportSheetBuilder->build($period);

        foreach (['017C', '021C', '022C', '025C', '026C'] as $code) {
            if ($site = $sites->get($code)) {
                $definitions[] = $this->rincianSheetBuilder->build($period, $site);
            }
        }

        foreach (['017C', '021C', '022C', '025C'] as $code) {
            if ($site = $sites->get($code)) {
                $definitions[] = $this->pnlSheetBuilder->build($period, $site);
            }
        }

        if ($aps = $sites->get('APS')) {
            $definitions[] = $this->rincianSheetBuilder->build($period, $aps);
            $definitions[] = $this->pnlSheetBuilder->build($period, $aps);
        }

        if ($ho = $sites->get('HO')) {
            $definitions[] = $this->rincianSheetBuilder->build($period, $ho);
            $definitions[] = $this->pnlSheetBuilder->build($period, $ho);
        }
        if ($jkt = $sites->get('JKT')) {
            $definitions[] = $this->rincianSheetBuilder->build($period, $jkt);
            $definitions[] = $this->pnlSheetBuilder->build($period, $jkt);
        }

        $definitions[] = $this->summaryPnlSheetBuilder->build($period);

        if ($site026 = $sites->get('026C')) {
            $definitions[] = $this->rincianSheetBuilder->build($period, $site026);
            $definitions[] = $this->pnlSheetBuilder->build($period, $site026);
        }

        if ($site023 = $sites->get('023C')) {
            $definitions[] = $this->rincianSheetBuilder->build($period, $site023);
            $definitions[] = $this->pnlSheetBuilder->build($period, $site023);
        }

        return $definitions;
    }

    private function mergeStreamingSheets(string $styledPath, array $streamingSheets): void
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($styledPath);

        foreach ($streamingSheets as $definition) {
            $tempPath = storage_path('app/reports/temp-'.uniqid().'.xlsx');
            $this->streamingRenderer->newWorkbook();
            $this->streamingRenderer->addSheet($definition->name, $definition);
            $this->streamingRenderer->save($tempPath);

            $streamed = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempPath);
            $streamedSheet = $streamed->getActiveSheet();
            $newSheet = $spreadsheet->createSheet();
            $newSheet->setTitle(mb_substr($definition->name, 0, 31));

            foreach ($streamedSheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                foreach ($row->getCellIterator() as $cell) {
                    $newSheet->setCellValue(
                        $cell->getColumn().$rowIndex,
                        $cell->getValue()
                    );
                }
            }

            @unlink($tempPath);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($styledPath);
    }
}
