<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\DeliverReportPackageJob;
use App\Jobs\GenerateReportArtifactsJob;
use App\Models\ApprovalStep;
use App\Models\ProjectSite;
use App\Models\ReportPackage;
use App\Models\ReportPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.generate');
    }

    public function index()
    {
        return Inertia::render('Reports/Index', [
            'packages' => ReportPackage::with('reportPeriod')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Reports/Builder', [
            'periods' => ReportPeriod::orderByDesc('year')->orderByDesc('month')->get(),
            'sites' => ProjectSite::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_period_id' => 'required|exists:report_periods,id',
        ]);

        $package = ReportPackage::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        $sites = ProjectSite::where('is_active', true)->get();
        foreach ($sites as $i => $site) {
            ApprovalStep::create([
                'report_package_id' => $package->id,
                'project_site_id' => $site->id,
                'step_order' => $i + 1,
                'approver_role' => 'Site Accountant',
            ]);
        }
        ApprovalStep::create([
            'report_package_id' => $package->id,
            'step_order' => $sites->count() + 1,
            'approver_role' => 'Finance Manager',
        ]);

        return redirect()->route('reports.show', $package);
    }

    public function show(ReportPackage $reportPackage)
    {
        return Inertia::render('Reports/Studio', [
            'package' => $reportPackage->load(['reportPeriod', 'artifacts', 'approvalSteps.projectSite']),
        ]);
    }

    public function generate(ReportPackage $reportPackage)
    {
        GenerateReportArtifactsJob::dispatch($reportPackage);

        return back()->with('success', 'Report generation queued.');
    }

    public function deliver(Request $request, ReportPackage $reportPackage)
    {
        $this->middleware('permission:reports.deliver');
        DeliverReportPackageJob::dispatch($reportPackage, $request->input('recipients', []));

        return back()->with('success', 'Delivery queued.');
    }
}
