<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DailyProductionRepository
{
    public function productionForSite(string $siteCode, int $year, int $month): float
    {
        $record = DB::connection('daily_production')
            ->table('production_records')
            ->join('sites', 'production_records.site_id', '=', 'sites.id')
            ->where('sites.code', $siteCode)
            ->whereYear('production_records.record_date', $year)
            ->whereMonth('production_records.record_date', $month)
            ->sum('production_records.tonnage');

        return (float) $record;
    }

    public function fuelForSite(string $siteCode, int $year, int $month): float
    {
        $record = DB::connection('daily_production')
            ->table('fuel_records')
            ->join('sites', 'fuel_records.site_id', '=', 'sites.id')
            ->where('sites.code', $siteCode)
            ->whereYear('fuel_records.record_date', $year)
            ->whereMonth('fuel_records.record_date', $month)
            ->sum('fuel_records.liters');

        return (float) $record;
    }

    public function obRemovalBcm(string $siteCode, int $year, int $month): float
    {
        $record = DB::connection('daily_production')
            ->table('production_records')
            ->join('sites', 'production_records.site_id', '=', 'sites.id')
            ->where('sites.code', $siteCode)
            ->whereYear('production_records.record_date', $year)
            ->whereMonth('production_records.record_date', $month)
            ->sum('production_records.ob_removal_bcm');

        return (float) $record;
    }

    public function budgetTarget(string $siteCode, int $year, int $month, string $metric): ?float
    {
        $target = DB::connection('daily_production')
            ->table('plan_targets')
            ->join('monthly_plans', 'plan_targets.monthly_plan_id', '=', 'monthly_plans.id')
            ->join('sites', 'monthly_plans.site_id', '=', 'sites.id')
            ->where('sites.code', $siteCode)
            ->where('monthly_plans.year', $year)
            ->where('monthly_plans.month', $month)
            ->where('plan_targets.metric', $metric)
            ->value('plan_targets.target_value');

        return $target !== null ? (float) $target : null;
    }

    public function siteMapping(string $arkledgerSiteCode): ?string
    {
        return DB::connection('daily_production')
            ->table('project_site_mappings')
            ->where('arkledger_code', $arkledgerSiteCode)
            ->value('daily_production_code');
    }
}
