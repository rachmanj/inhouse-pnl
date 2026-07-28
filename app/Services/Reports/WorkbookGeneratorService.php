<?php

namespace App\Services\Reports;

use App\Models\ReportPackage;
use App\Models\ReportPeriod;
use App\Models\ProjectSite;
use Illuminate\Support\Facades\Storage;

class WorkbookGeneratorService
{
    public function __construct(
        private PhpSpreadsheetExcelRenderer $styledRenderer,
        private OpenSpoutExcelRenderer $streamingRenderer,
    ) {}

    public function generate(ReportPackage $package): string
    {
        $period = $package->reportPeriod;
        $this->styledRenderer->newWorkbook();

        $sheetMap = $this->sheetDefinitions($period);

        foreach ($sheetMap as $name => $definition) {
            $renderer = $definition->engine === 'streaming' ? $this->streamingRenderer : $this->styledRenderer;
            if ($definition->engine === 'streaming') {
                $this->streamingRenderer->newWorkbook();
            }
            $renderer->addSheet($name, $definition);
        }

        $path = storage_path('app/reports/package_'.$package->id.'_'.time().'.xlsx');
        @mkdir(dirname($path), 0755, true);

        return $this->styledRenderer->save($path);
    }

    private function sheetDefinitions(ReportPeriod $period): array
    {
        $sites = ProjectSite::where('is_active', true)->orderBy('sort_order')->get();
        $sheets = [];

        $sheets['JOURNAL ENTRY'] = new SheetDefinition('JOURNAL ENTRY', [['Reference', 'Account', 'Debit', 'Credit']]);
        $sheets['PETTY CASH SUMMARY'] = new SheetDefinition('PETTY CASH SUMMARY', [['Site', 'Opening', 'Closing']]);
        $sheets['SPT & PAYMENT'] = new SheetDefinition('SPT & PAYMENT', [['Tax Type', 'Amount', 'Date']], engine: 'streaming');
        $sheets['MONTHLY TAX REPORT'] = new SheetDefinition('MONTHLY TAX REPORT', [['Tax Type', 'Reported', 'Paid']]);

        foreach ($sites as $site) {
            $sheets["Rincian {$site->code}"] = new SheetDefinition("Rincian {$site->code}", [['Account', 'Amount']]);
            $sheets["P&L {$site->code}"] = new SheetDefinition("P&L {$site->code}", [['Line', 'Amount']]);
        }

        $sheets['SUMMARY P&L'] = new SheetDefinition('SUMMARY P&L', [['Line', 'Consolidated']]);

        return $sheets;
    }
}
