<?php

namespace App\Http\Controllers\Pnl;

use App\Http\Controllers\Controller;
use App\Models\PnlSnapshot;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Pnl\SitePnlPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SitePnlController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pnl.view-own-site|pnl.view-all-sites');
    }

    public function show(Request $request, ProjectSite $projectSite, SitePnlPresenter $presenter): Response
    {
        $period = $this->resolvePeriod($request);
        $view = $request->query('view', 'pnl');

        if (! in_array($view, ['rincian', 'pnl'], true)) {
            $view = 'pnl';
        }

        $current = $this->findOrEmptySnapshot($period, $projectSite);
        $baselinePeriod = ReportPeriod::where('year', $period->baseline_year)
            ->where('month', $period->month)
            ->first();

        $baseline = $baselinePeriod
            ? $this->findOrEmptySnapshot($baselinePeriod, $projectSite)
            : PnlSnapshot::make(['report_period_id' => $period->id, 'project_site_id' => $projectSite->id]);

        $current->setRelation('reportPeriod', $period);
        $baseline->setRelation('reportPeriod', $baselinePeriod ?? $period);

        return Inertia::render('Pnl/Site', [
            'site' => $projectSite,
            'period' => $period,
            'view' => $view,
            'grid' => $presenter->present($baseline, $current, $view),
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

    private function findOrEmptySnapshot(ReportPeriod $period, ProjectSite $site): PnlSnapshot
    {
        return PnlSnapshot::where('report_period_id', $period->id)
            ->where('project_site_id', $site->id)
            ->with('lines')
            ->first()
            ?? PnlSnapshot::make([
                'report_period_id' => $period->id,
                'project_site_id' => $site->id,
            ])->setRelation('lines', collect());
    }
}
