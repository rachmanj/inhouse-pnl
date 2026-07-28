<?php

namespace App\Http\Controllers;

use App\Models\PnlSnapshot;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $period = $this->resolvePeriod($request);

        $consolidated = PnlSnapshot::where('report_period_id', $period->id)
            ->whereNull('project_site_id')
            ->with('lines.pnlLine')
            ->first();

        $kpis = $this->buildKpis($consolidated, $period);
        $trend = $this->buildTrendSeries($period);
        $siteStatus = $this->buildSiteStatusBoard($period);

        return Inertia::render('Dashboard/Index', [
            'period' => $period,
            'kpis' => $kpis,
            'trend' => $trend,
            'siteStatus' => $siteStatus,
            'insights' => [],
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

    private function buildKpis(?PnlSnapshot $snapshot, ReportPeriod $period): array
    {
        $lines = $snapshot?->lines->keyBy(fn ($l) => $l->pnlLine->code ?? '') ?? collect();

        $revenue = (float) ($lines->get('REVENUE_ENGINEERING')?->amount ?? 0);
        $costIph = (float) ($lines->get('COST_IPH')?->amount ?? 0);
        $netPnl = (float) ($lines->get('PROFIT_LOSS')?->amount ?? 0);

        $baselineSnapshot = PnlSnapshot::where('report_period_id', $period->id)
            ->whereNull('project_site_id')
            ->first();

        $baselineNet = 0;
        if ($baselineSnapshot) {
            $baselineNet = (float) ($baselineSnapshot->lines()
                ->whereHas('pnlLine', fn ($q) => $q->where('code', 'PROFIT_LOSS'))
                ->value('amount') ?? 0);
        }

        $vsBaseline = $baselineNet != 0
            ? round((($netPnl - $baselineNet) / abs($baselineNet)) * 100, 2)
            : 0;

        return [
            'revenue' => $revenue,
            'cost_iph' => $costIph,
            'net_pnl' => $netPnl,
            'vs_baseline_percent' => $vsBaseline,
        ];
    }

    private function buildTrendSeries(ReportPeriod $period): array
    {
        $snapshots = PnlSnapshot::whereNull('project_site_id')
            ->whereHas('reportPeriod', fn ($q) => $q->where('year', $period->year))
            ->with(['reportPeriod', 'lines' => fn ($q) => $q->whereHas('pnlLine', fn ($lq) => $lq->where('code', 'PROFIT_LOSS'))])
            ->get();

        return $snapshots->map(fn ($s) => [
            'month' => $s->reportPeriod->month,
            'amount' => (float) ($s->lines->first()?->amount ?? 0),
        ])->values()->all();
    }

    private function buildSiteStatusBoard(ReportPeriod $period): array
    {
        return ProjectSite::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (ProjectSite $site) use ($period) {
                $snapshot = PnlSnapshot::where('report_period_id', $period->id)
                    ->where('project_site_id', $site->id)
                    ->first();

                return [
                    'code' => $site->code,
                    'name' => $site->name,
                    'snapshot_status' => $snapshot?->status ?? 'none',
                    'period_status' => $period->status,
                ];
            })
            ->all();
    }
}
