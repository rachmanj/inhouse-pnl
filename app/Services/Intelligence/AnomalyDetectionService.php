<?php

namespace App\Services\Intelligence;

use App\Jobs\NotifyInsightJob;
use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\AnomalyAlert;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Repositories\DailyProductionRepository;
use Illuminate\Support\Collection;

class AnomalyDetectionService
{
    public function __construct(
        private DailyProductionRepository $dailyProduction,
        private RatioAnalyticsService $ratioAnalytics,
    ) {}

    public function scan(ReportPeriod $period): void
    {
        $sites = ProjectSite::where('is_active', true)->get();
        $months = config('intelligence.anomaly.trailing_months');
        $threshold = config('intelligence.anomaly.z_score_threshold');

        foreach ($sites as $site) {
            $accounts = Account::where('is_postable', true)->get();

            foreach ($accounts as $account) {
                $history = $this->trailingBalances($period, $site->id, $account->id, $months);
                if ($history->count() < 3) {
                    continue;
                }

                $mean = $history->avg();
                $stdDev = $this->stdDev($history, $mean);
                $current = (float) AccountBalance::where('report_period_id', $period->id)
                    ->where('project_site_id', $site->id)
                    ->where('account_id', $account->id)
                    ->value('balance');

                if ($stdDev == 0.0) {
                    continue;
                }

                $zScore = ($current - $mean) / $stdDev;
                if (abs($zScore) < $threshold) {
                    continue;
                }

                if ($account->sap_code === '51100') {
                    $obRemoval = $this->dailyProduction->obRemovalBcm($site->code, $period->year, $period->month);
                    $priorOb = $this->dailyProduction->obRemovalBcm(
                        $site->code,
                        $period->month === 1 ? $period->year - 1 : $period->year,
                        $period->month === 1 ? 12 : $period->month - 1
                    );
                    $fuelPct = $mean != 0.0 ? (($current - $mean) / abs($mean)) * 100 : 0;
                    $obPct = $priorOb != 0.0 ? (($obRemoval - $priorOb) / abs($priorOb)) * 100 : 0;

                    if (abs($fuelPct) > abs($obPct) * 2) {
                        $explanation = sprintf(
                            '%+.0f%% fuel cost, but OB removal only %+.0f%%',
                            $fuelPct,
                            $obPct
                        );
                    } else {
                        continue;
                    }
                } else {
                    $explanation = sprintf(
                        'Account %s deviates %.1fσ from trailing %d-month mean',
                        $account->sap_code,
                        $zScore,
                        $months
                    );
                }

                AnomalyAlert::create([
                    'report_period_id' => $period->id,
                    'project_site_id' => $site->id,
                    'account_id' => $account->id,
                    'metric' => $account->sap_code,
                    'observed_value' => $current,
                    'expected_value' => $mean,
                    'z_score' => round($zScore, 4),
                    'explanation' => $explanation,
                    'status' => 'open',
                ]);

                NotifyInsightJob::dispatch('anomaly', $explanation);
            }

            $this->ratioAnalytics->computeForSite($period, $site);
        }
    }

    private function trailingBalances(ReportPeriod $period, int $siteId, int $accountId, int $months): Collection
    {
        $periods = ReportPeriod::where(function ($q) use ($period) {
            $q->where('year', '<', $period->year)
                ->orWhere(function ($q2) use ($period) {
                    $q2->where('year', $period->year)->where('month', '<', $period->month);
                });
        })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit($months)
            ->pluck('id');

        return AccountBalance::whereIn('report_period_id', $periods)
            ->where('project_site_id', $siteId)
            ->where('account_id', $accountId)
            ->pluck('balance')
            ->map(fn ($v) => (float) $v);
    }

    private function stdDev(Collection $values, float $mean): float
    {
        if ($values->count() < 2) {
            return 0.0;
        }

        $variance = $values->reduce(fn ($carry, $v) => $carry + (($v - $mean) ** 2), 0.0) / ($values->count() - 1);

        return sqrt($variance);
    }
}
