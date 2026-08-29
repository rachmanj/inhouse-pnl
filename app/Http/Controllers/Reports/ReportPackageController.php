<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\DeliverReportPackageJob;
use App\Jobs\GenerateReportArtifactsJob;
use App\Models\ApprovalStep;
use App\Models\ProjectSite;
use App\Models\ReportPackage;
use App\Models\ReportPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.generate')->except(['deliver']);
        $this->middleware('permission:reports.deliver')->only(['deliver']);
    }

    public function index(): Response
    {
        return Inertia::render('Reports/Index', [
            'packages' => ReportPackage::with(['reportPeriod', 'createdBy'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Reports/Builder', [
            'periods' => ReportPeriod::orderByDesc('year')->orderByDesc('month')->get(),
            'sites' => ProjectSite::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_period_id' => ['required', 'exists:report_periods,id'],
        ]);

        $package = DB::transaction(function () use ($validated, $request) {
            $package = ReportPackage::create([
                'report_period_id' => $validated['report_period_id'],
                'status' => 'draft',
                'created_by' => $request->user()->id,
            ]);

            $sites = ProjectSite::where('is_active', true)->orderBy('sort_order')->get();
            $order = 1;

            foreach ($sites as $site) {
                ApprovalStep::create([
                    'report_package_id' => $package->id,
                    'project_site_id' => $site->id,
                    'step_order' => $order++,
                    'approver_role' => 'Site Accountant',
                    'status' => 'pending',
                ]);
            }

            ApprovalStep::create([
                'report_package_id' => $package->id,
                'project_site_id' => null,
                'step_order' => $order,
                'approver_role' => 'Finance Manager',
                'status' => 'pending',
            ]);

            return $package;
        });

        return redirect()->route('reports.show', $package);
    }

    public function show(ReportPackage $reportPackage): Response
    {
        return Inertia::render('Reports/Studio', [
            'package' => $reportPackage->load([
                'reportPeriod',
                'artifacts',
                'approvalSteps.projectSite',
                'approvalSteps.actedBy',
                'deliveryLogs',
            ]),
        ]);
    }

    public function generate(ReportPackage $reportPackage): RedirectResponse
    {
        GenerateReportArtifactsJob::dispatch($reportPackage);

        return back()->with('success', 'Report generation queued.');
    }

    public function deliver(Request $request, ReportPackage $reportPackage): RedirectResponse
    {
        $validated = $request->validate([
            'channel' => ['required', 'in:email,whatsapp,telegram'],
            'recipient' => ['nullable', 'string'],
        ]);

        DeliverReportPackageJob::dispatch(
            $reportPackage,
            $validated['channel'],
            $validated['recipient'] ?? null
        );

        return back()->with('success', 'Delivery queued.');
    }
}
