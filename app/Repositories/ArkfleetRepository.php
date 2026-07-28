<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArkfleetRepository
{
    public function depreciationEntriesForPeriod(int $year, int $month): Collection
    {
        return DB::connection('arkfleet')
            ->table('depreciation_entries')
            ->whereYear('period_date', $year)
            ->whereMonth('period_date', $month)
            ->get();
    }

    public function equipmentByProjectCode(string $projectCode): ?object
    {
        return DB::connection('arkfleet')
            ->table('equipment')
            ->join('projects', 'equipment.project_id', '=', 'projects.id')
            ->where('projects.code', $projectCode)
            ->select('equipment.*')
            ->first();
    }

    public function hmKmReadings(int $equipmentId, int $year, int $month): Collection
    {
        return DB::connection('arkfleet')
            ->table('equipment_hm_km_readings')
            ->where('equipment_id', $equipmentId)
            ->whereYear('reading_date', $year)
            ->whereMonth('reading_date', $month)
            ->get();
    }
}
