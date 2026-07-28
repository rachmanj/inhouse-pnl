<?php

namespace App\Services\Reports\Sheets;

use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Reports\SheetDefinition;

interface SheetBuilderInterface
{
    public function build(ReportPeriod $period, ?ProjectSite $site = null): SheetDefinition;
}
