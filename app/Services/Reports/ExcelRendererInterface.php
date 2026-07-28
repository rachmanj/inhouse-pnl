<?php

namespace App\Services\Reports;

interface ExcelRendererInterface
{
    public function newWorkbook(): void;

    public function addSheet(string $name, SheetDefinition $definition): void;

    public function save(string $path): string;
}
