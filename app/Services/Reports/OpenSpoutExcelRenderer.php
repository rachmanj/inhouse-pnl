<?php

namespace App\Services\Reports;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class OpenSpoutExcelRenderer implements ExcelRendererInterface
{
    private ?Writer $writer = null;

    private string $tempPath;

    public function newWorkbook(): void
    {
        $this->tempPath = storage_path('app/reports/'.uniqid('workbook_', true).'.xlsx');
        $directory = dirname($this->tempPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->writer = new Writer;
        $this->writer->openToFile($this->tempPath);
    }

    public function addSheet(string $name, SheetDefinition $definition): void
    {
        if ($this->writer === null) {
            $this->newWorkbook();
        }

        $this->writer->getCurrentSheet()->setName(mb_substr($name, 0, 31));

        foreach ($definition->headerRows as $headerRow) {
            $this->writer->addRow(Row::fromValues($headerRow));
        }

        if ($definition->columnGroups !== []) {
            $groupHeader = [];
            foreach ($definition->columnGroups as $group) {
                $groupHeader[] = $group['title'] ?? '';
                $columns = $group['columns'] ?? [];
                for ($i = 1; $i < count($columns); $i++) {
                    $groupHeader[] = '';
                }
            }
            $this->writer->addRow(Row::fromValues($groupHeader));

            $columnHeader = [];
            foreach ($definition->columnGroups as $group) {
                foreach ($group['columns'] ?? [] as $column) {
                    $columnHeader[] = $column;
                }
            }
            $this->writer->addRow(Row::fromValues($columnHeader));
        }

        foreach ($definition->dataRows as $dataRow) {
            $this->writer->addRow(Row::fromValues($dataRow));
        }

        if ($definition->totalsRow !== null) {
            $this->writer->addRow(Row::fromValues($definition->totalsRow));
        }

        if ($definition !== end($definition->dataRows)) {
            $this->writer->addNewSheetAndMakeItCurrent();
        }
    }

    public function save(string $path): string
    {
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->writer?->close();

        if (isset($this->tempPath) && file_exists($this->tempPath)) {
            rename($this->tempPath, $path);
        }

        return realpath($path) ?: $path;
    }
}
