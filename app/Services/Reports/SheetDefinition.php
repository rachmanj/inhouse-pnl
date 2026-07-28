<?php

namespace App\Services\Reports;

class SheetDefinition
{
    public function __construct(
        public string $name,
        public array $headerRows = [],
        public array $columnGroups = [],
        public array $dataRows = [],
        public ?array $totalsRow = null,
        public string $engine = 'styled',
    ) {}
}
