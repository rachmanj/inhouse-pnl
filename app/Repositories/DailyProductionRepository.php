<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class DailyProductionRepository
{
    public function fuelForSite(string $siteCode, int $year, int $month): float
    {
        try {
            return (float) DB::connection('daily_production')
                ->table('fuel_records')
                ->where('site_code', $siteCode)
                ->whereYear('record_date', $year)
                ->whereMonth('record_date', $month)
                ->sum('quantity');
        } catch (\Throwable) {
            return 0;
        }
    }
}
