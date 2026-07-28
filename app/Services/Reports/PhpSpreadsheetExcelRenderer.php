<?php

namespace App\Services\Reports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PhpSpreadsheetExcelRenderer implements ExcelRendererInterface
{
    private Spreadsheet $spreadsheet;

    private int $sheetIndex = 0;

    public function newWorkbook(): void
    {
        $this->spreadsheet = new Spreadsheet;
        $this->sheetIndex = 0;
    }

    public function addSheet(string $name, SheetDefinition $definition): void
    {
        $sheet = $this->sheetIndex === 0
            ? $this->spreadsheet->getActiveSheet()
            : $this->spreadsheet->createSheet($this->sheetIndex);

        $sheet->setTitle(mb_substr($name, 0, 31));
        $row = 1;

        foreach ($definition->headerRows as $headerRow) {
            $col = 1;
            foreach ($headerRow as $cell) {
                $sheet->setCellValue([$col, $row], $cell);
                $col++;
            }
            $row++;
        }

        if ($definition->columnGroups !== []) {
            $col = 1;
            foreach ($definition->columnGroups as $group) {
                $startCol = $col;
                $sheet->setCellValue([$col, $row], $group['title'] ?? '');
                $columns = $group['columns'] ?? [];
                if (count($columns) > 1) {
                    $sheet->mergeCellsByColumnAndRow($startCol, $row, $startCol + count($columns) - 1, $row);
                }
                $col += max(count($columns), 1);
            }
            $row++;

            $col = 1;
            foreach ($definition->columnGroups as $group) {
                foreach ($group['columns'] ?? [] as $column) {
                    $sheet->setCellValue([$col, $row], $column);
                    $col++;
                }
            }
            $row++;
        }

        foreach ($definition->dataRows as $dataRow) {
            $col = 1;
            foreach ($dataRow as $cell) {
                $sheet->setCellValue([$col, $row], $cell);
                $col++;
            }
            $row++;
        }

        if ($definition->totalsRow !== null) {
            $col = 1;
            foreach ($definition->totalsRow as $cell) {
                $sheet->setCellValue([$col, $row], $cell);
                $col++;
            }
            $sheet->getStyle("A{$row}:".$sheet->getHighestColumn().$row)
                ->getFont()->setBold(true);
        }

        $headerRange = 'A1:'.$sheet->getHighestColumn().'2';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E1F2');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $this->sheetIndex++;
    }

    public function save(string $path): string
    {
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $writer = new Xlsx($this->spreadsheet);
        $writer->save($path);

        return realpath($path) ?: $path;
    }
}
