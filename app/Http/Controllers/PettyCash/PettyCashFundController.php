<?php

namespace App\Http\Controllers\PettyCash;

use App\Http\Controllers\Controller;
use App\Models\PettyCashFund;
use App\Models\ReportPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PettyCashFundController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pettycash.manage');
    }

    public function index(Request $request): Response
    {
        $period = $this->resolvePeriod($request);

        $funds = PettyCashFund::with(['projectSite', 'reportPeriod'])
            ->where('report_period_id', $period->id)
            ->withSum('expenses', 'amount')
            ->orderBy('project_site_id')
            ->get();

        return Inertia::render('PettyCash/Index', [
            'period' => $period,
            'funds' => $funds,
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
}
