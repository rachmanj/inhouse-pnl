<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ArkfleetRepository
{
    public function depreciationEntries(int $year, int $month): array
    {
        try {
            return DB::connection('arkfleet')
                ->table('depreciation_entries')
                ->whereYear('period_date', $year)
                ->whereMonth('period_date', $month)
                ->get()
                ->toArray();
        } catch (\Throwable) {
            return [];
        }
    }
}
