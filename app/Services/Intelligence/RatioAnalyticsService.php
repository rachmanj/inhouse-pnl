<?php

namespace App\Services\Intelligence;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\PnlLine;
use App\Models\PnlSnapshot;
use App\Models\ProjectSite;
use App\Models\RatioSnapshot;
use App\Models\ReportPeriod;
use App\Repositories\DailyProductionRepository;

class RatioAnalyticsService
{
    public function __construct(
        private DailyProductionRepository $dailyProduction,
    ) {}

    public function computeForSite(ReportPeriod $period, ProjectSite $site): void
    {
        $snapshot = PnlSnapshot::where('report_period_id', $period->id)
            ->where('project_site_id', $site->id)
            ->with('lines.pnlLine')
            ->first();

        $revenue = $this->lineAmount($snapshot, 'REVENUE');
        $fuel = $this->accountBalance($period, $site, '51100');
        $depreciation = $this->lineAmount($snapshot, 'DEPRECIATION');
        $production = $this->dailyProduction->productionForSite($site->code, $period->year, $period->month);
        $fuelLiters = $this->dailyProduction->fuelForSite($site->code, $period->year, $period->month);
        $obRemoval = $this->dailyProduction->obRemovalBcm($site->code, $period->year, $period->month);

        $ratios = [
            'COST_REVENUE' => $revenue != 0.0 ? ($fuel / $revenue) : 0,
            'FUEL_EFFICIENCY' => $fuelLiters != 0.0 ? ($production / $fuelLiters) : 0,
            'STRIPPING_RATIO' => $production != 0.0 ? ($obRemoval / $production) : 0,
            'FUEL_COST_PER_TON' => $production != 0.0 ? ($fuel / $production) : 0,
            'DEPR_INTENSITY' => $revenue != 0.0 ? ($depreciation / $revenue) : 0,
            'GROSS_MARGIN' => $revenue != 0.0 ? (($revenue - $fuel) / $revenue) : 0,
        ];

        foreach ($ratios as $code => $value) {
            RatioSnapshot::updateOrCreate(
                [
                    'report_period_id' => $period->id,
                    'project_site_id' => $site->id,
                    'ratio_code' => $code,
                ],
                [
                    'value' => round($value, 4),
                    'computed_at' => now(),
                ]
            );
        }
    }

    private function lineAmount(?PnlSnapshot $snapshot, string $code): float
    {
        if (! $snapshot) {
            return 0.0;
        }

        $line = PnlLine::where('code', $code)->first();
        if (! $line) {
            return 0.0;
        }

        return (float) $snapshot->lines->where('pnl_line_id', $line->id)->sum('amount');
    }

    private function accountBalance(ReportPeriod $period, ProjectSite $site, string $sapCode): float
    {
        $account = Account::where('sap_code', $sapCode)->first();
        if (! $account) {
            return 0.0;
        }

        return (float) AccountBalance::where('report_period_id', $period->id)
            ->where('project_site_id', $site->id)
            ->where('account_id', $account->id)
            ->sum('balance');
    }
}
