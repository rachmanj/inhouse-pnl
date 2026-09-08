<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportPeriodStatus;
use App\Http\Controllers\Controller;
use App\Models\ReportPeriod;
use App\Services\Periods\PeriodStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportPeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:periods.manage|periods.lock');
    }

    public function index(): Response
    {
        return Inertia::render('Admin/ReportPeriods/Index', [
            'periods' => ReportPeriod::orderByDesc('year')->orderByDesc('month')->get(),
        ]);
    }

    public function transition(Request $request, ReportPeriod $reportPeriod, PeriodStateService $periodState): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_review,approved,delivered,locked'],
        ]);

        $periodState->transition(
            $reportPeriod,
            ReportPeriodStatus::from($validated['status']),
            $request->user()
        );

        return back()->with('success', 'Period status updated.');
    }
}
