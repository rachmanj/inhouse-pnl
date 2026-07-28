<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use App\Models\ReportPeriod;
use App\Models\TaxFiling;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaxFilingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tax.manage');
    }

    public function index(Request $request): Response
    {
        $period = $this->resolvePeriod($request);
        $taxType = $request->query('tax_type');

        $filings = TaxFiling::with(['projectSite', 'reportPeriod'])
            ->where('report_period_id', $period->id)
            ->when($taxType, fn ($q) => $q->where('tax_type', $taxType))
            ->orderBy('due_date')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Tax/Index', [
            'period' => $period,
            'taxType' => $taxType,
            'filings' => $filings,
        ]);
    }

    public function calendar(Request $request): Response
    {
        $filings = TaxFiling::with(['projectSite', 'reportPeriod'])
            ->when($request->query('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->orderBy('due_date')
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('Tax/Calendar', [
            'filings' => $filings,
            'filters' => $request->only('status'),
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
