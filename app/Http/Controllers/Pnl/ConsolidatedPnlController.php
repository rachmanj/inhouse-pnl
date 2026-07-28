<?php

namespace App\Http\Controllers\Pnl;

use App\Http\Controllers\Controller;
use App\Models\PnlLine;
use App\Models\PnlSnapshot;
use App\Models\PnlSnapshotLine;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Pnl\SitePnlPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConsolidatedPnlController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pnl.view-all-sites');
    }

    public function show(Request $request, SitePnlPresenter $presenter): Response
    {
        $period = $this->resolvePeriod($request);
        $excludeSites = $request->query('exclude_sites', []);

        $current = $this->buildConsolidatedSnapshot($period, $excludeSites);
        $baselinePeriod = ReportPeriod::where('year', $period->baseline_year)
            ->where('month', $period->month)
            ->first();

        $baseline = $baselinePeriod
            ? $this->buildConsolidatedSnapshot($baselinePeriod, $excludeSites)
            : PnlSnapshot::make(['report_period_id' => $period->id]);

        $current->setRelation('reportPeriod', $period);
        $baseline->setRelation('reportPeriod', $baselinePeriod ?? $period);

        $contributions = $this->siteContributions($period, $excludeSites);

        return Inertia::render('Pnl/Consolidated', [
            'period' => $period,
            'excludeSites' => $excludeSites,
            'grid' => $presenter->present($baseline, $current, $request->query('view', 'pnl')),
            'contributions' => $contributions,
        ]);
    }

    private function resolvePeriod(Request $request): ReportPeriod
    {
        if ($request->filled('period_id')) {
            return ReportPeriod::findOrFail($request->period_id);
        }

        return ReportPeriod::whereIn('status', ['open', 'in_review'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first()
            ?? ReportPeriod::orderByDesc('year')->orderByDesc('month')->firstOrFail();
    }

    private function buildConsolidatedSnapshot(ReportPeriod $period, array $excludeSites): PnlSnapshot
    {
        if (empty($excludeSites)) {
            $snapshot = PnlSnapshot::where('report_period_id', $period->id)
                ->whereNull('project_site_id')
                ->with('lines')
                ->first();

            if ($snapshot) {
                return $snapshot;
            }
        }

        $sites = ProjectSite::where('is_active', true)
            ->when(! empty($excludeSites), fn ($q) => $q->whereNotIn('code', $excludeSites))
            ->pluck('id');

        $siteSnapshots = PnlSnapshot::where('report_period_id', $period->id)
            ->whereIn('project_site_id', $sites)
            ->with('lines')
            ->get();

        $consolidatedLines = collect();

        foreach ($siteSnapshots as $siteSnapshot) {
            foreach ($siteSnapshot->lines as $line) {
                $key = $line->pnl_line_id.'_'.$line->year.'_'.$line->month;
                $existing = $consolidatedLines->get($key);

                if ($existing) {
                    $existing->amount = (float) $existing->amount + (float) $line->amount;
                } else {
                    $consolidatedLines->put($key, new PnlSnapshotLine([
                        'pnl_line_id' => $line->pnl_line_id,
                        'year' => $line->year,
                        'month' => $line->month,
                        'amount' => $line->amount,
                    ]));
                }
            }
        }

        return PnlSnapshot::make([
            'report_period_id' => $period->id,
            'project_site_id' => null,
        ])->setRelation('lines', $consolidatedLines->values());
    }

    private function siteContributions(ReportPeriod $period, array $excludeSites): array
    {
        $revenueLineId = PnlLine::where('code', 'REVENUE_ENGINEERING')->value('id');

        return ProjectSite::where('is_active', true)
            ->when(! empty($excludeSites), fn ($q) => $q->whereNotIn('code', $excludeSites))
            ->orderBy('sort_order')
            ->get()
            ->map(function (ProjectSite $site) use ($period, $revenueLineId) {
                $snapshot = PnlSnapshot::where('report_period_id', $period->id)
                    ->where('project_site_id', $site->id)
                    ->first();

                $revenue = 0;
                if ($snapshot && $revenueLineId) {
                    $revenue = (float) ($snapshot->lines()
                        ->where('pnl_line_id', $revenueLineId)
                        ->where('year', $period->year)
                        ->where('month', $period->month)
                        ->value('amount') ?? 0);
                }

                return [
                    'code' => $site->code,
                    'name' => $site->name,
                    'revenue' => $revenue,
                ];
            })
            ->all();
    }
}
