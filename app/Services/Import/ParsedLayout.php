<?php

namespace App\Services\Import;

class ParsedLayout
{
    public function __construct(
        public int $headerRow,
        public int $dataStartRow,
        public array $headers,
        public ?int $sapMarkerRow = null,
        public ?array $columnMap = null,
    ) {}
}
