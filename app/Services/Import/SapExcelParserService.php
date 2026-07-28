<?php

namespace App\Services\Import;

use App\Models\ImportColumnMap;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SapExcelParserService
{
    public function detectLayout(string $filePath): ParsedLayout
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $maxRow = min($sheet->getHighestRow(), 50);

        $sapMarkerRow = null;
        $headerRow = 1;

        for ($row = 1; $row <= $maxRow; $row++) {
            $rowValues = $this->readRow($sheet, $row);
            foreach ($rowValues as $value) {
                if (is_string($value) && preg_match('/^SAP$/i', trim($value))) {
                    $sapMarkerRow = $row;
                    $headerRow = $row + 1;
                    break 2;
                }
            }
        }

        $headers = $this->readRow($sheet, $headerRow);

        return new ParsedLayout(
            headerRow: $headerRow,
            dataStartRow: $headerRow + 1,
            headers: $headers,
            sapMarkerRow: $sapMarkerRow,
        );
    }

    public function extractRows(string $filePath, ParsedLayout $layout): Collection
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $columnMap = $layout->columnMap ?? $this->guessColumnMap($layout);
        $rows = collect();

        $maxRow = $sheet->getHighestRow();

        for ($row = $layout->dataStartRow; $row <= $maxRow; $row++) {
            $rowData = $this->readRow($sheet, $row);

            if ($this->isEmptyRow($rowData)) {
                continue;
            }

            $accountCode = $this->cellValue($rowData, $columnMap['account_code'] ?? null);
            if (blank($accountCode)) {
                continue;
            }

            $rows->push([
                'row_number' => $row,
                'raw_account_code' => (string) $accountCode,
                'raw_account_name' => (string) ($this->cellValue($rowData, $columnMap['account_name'] ?? null) ?? ''),
                'raw_debit' => $this->numericValue($this->cellValue($rowData, $columnMap['debit'] ?? null)),
                'raw_credit' => $this->numericValue($this->cellValue($rowData, $columnMap['credit'] ?? null)),
                'raw_balance' => $this->numericValue($this->cellValue($rowData, $columnMap['balance'] ?? null)),
                'raw_payload' => $rowData,
            ]);
        }

        return $rows;
    }

    public function guessColumnMap(ParsedLayout $layout): array
    {
        $signature = hash('sha256', implode('|', array_map('strval', $layout->headers)));
        $cached = ImportColumnMap::where('source_signature', $signature)->first();

        if ($cached) {
            $cached->increment('times_used');

            return $cached->column_map;
        }

        $map = [];
        foreach ($layout->headers as $index => $header) {
            $normalized = Str::lower(trim((string) $header));

            if (Str::contains($normalized, ['account', 'kode', 'code', 'gl'])) {
                $map['account_code'] = $index;
            } elseif (Str::contains($normalized, ['name', 'nama', 'description'])) {
                $map['account_name'] = $index;
            } elseif (Str::contains($normalized, ['debit'])) {
                $map['debit'] = $index;
            } elseif (Str::contains($normalized, ['kredit', 'credit'])) {
                $map['credit'] = $index;
            } elseif (Str::contains($normalized, ['saldo', 'balance'])) {
                $map['balance'] = $index;
            }
        }

        ImportColumnMap::updateOrCreate(
            ['source_signature' => $signature],
            ['column_map' => $map, 'times_used' => 1]
        );

        return $map;
    }

    private function readRow($sheet, int $row): array
    {
        $highestColumn = $sheet->getHighestColumn();
        $values = [];

        foreach (range('A', $highestColumn) as $col) {
            $values[] = $sheet->getCell($col.$row)->getCalculatedValue();
        }

        return $values;
    }

    private function cellValue(array $row, ?int $index)
    {
        return $index !== null ? ($row[$index] ?? null) : null;
    }

    private function numericValue($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) str_replace([',', ' '], '', (string) $value);
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isEmpty();
    }
}
