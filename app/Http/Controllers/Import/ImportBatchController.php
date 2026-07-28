<?php

namespace App\Http\Controllers\Import;

use App\Actions\Import\ResolveAmbiguousMappingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ResolveMappingRequest;
use App\Http\Requests\Import\StoreImportBatchRequest;
use App\Jobs\MapAndValidateImportBatchJob;
use App\Jobs\StageImportBatchJob;
use App\Models\Account;
use App\Models\ImportBatch;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Models\SapStagingRow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ImportBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:imports.manage')->except(['create', 'store']);
        $this->middleware('permission:imports.create')->only(['create', 'store']);
    }

    public function index(): Response
    {
        return Inertia::render('Import/Index', [
            'batches' => ImportBatch::with(['reportPeriod', 'projectSite', 'triggeredBy'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Import/Create', [
            'periods' => ReportPeriod::orderByDesc('year')->orderByDesc('month')->get(),
            'sites' => ProjectSite::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreImportBatchRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('imports', 'local');

        $batch = ImportBatch::create([
            'report_period_id' => $request->report_period_id,
            'project_site_id' => $request->project_site_id,
            'source' => 'upload',
            'status' => 'pending',
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'triggered_by' => $request->user()->id,
            'started_at' => now(),
        ]);

        Bus::chain([
            new StageImportBatchJob($batch),
            new MapAndValidateImportBatchJob($batch),
        ])->dispatch();

        return redirect()->route('imports.show', $batch)
            ->with('success', 'Import started. Staging rows will appear shortly.');
    }

    public function show(ImportBatch $importBatch): Response
    {
        $importBatch->load(['reportPeriod', 'projectSite', 'triggeredBy']);

        return Inertia::render('Import/Show', [
            'batch' => $importBatch,
            'stagingRows' => $importBatch->stagingRows()
                ->with('mappedAccount')
                ->when(request('mapping_status'), fn ($q) => $q->where('mapping_status', request('mapping_status')))
                ->orderBy('row_number')
                ->paginate(50)
                ->withQueryString(),
            'accounts' => Account::orderBy('sap_code')->get(['id', 'sap_code', 'name']),
            'filters' => request()->only('mapping_status'),
        ]);
    }

    public function resolveMapping(
        ResolveMappingRequest $request,
        ImportBatch $importBatch,
        ResolveAmbiguousMappingAction $action,
    ): RedirectResponse {
        $row = SapStagingRow::where('import_batch_id', $importBatch->id)
            ->findOrFail($request->staging_row_id);

        $action->execute($row, $request->account_id);

        return back()->with('success', 'Account mapping resolved.');
    }

    public function confirm(ImportBatch $importBatch, ResolveAmbiguousMappingAction $action): RedirectResponse
    {
        $unresolved = $importBatch->stagingRows()
            ->whereIn('mapping_status', ['unmapped', 'ambiguous'])
            ->count();

        if ($unresolved > 0) {
            return back()->withErrors(['confirm' => 'Resolve all ambiguous mappings before confirming.']);
        }

        $action->confirmBatch($importBatch);

        return redirect()->route('imports.show', $importBatch)
            ->with('success', 'Import confirmed. Account balances are being updated.');
    }

    public function destroy(ImportBatch $importBatch): RedirectResponse
    {
        if (! in_array($importBatch->status, ['pending', 'failed'], true)) {
            return back()->withErrors(['destroy' => 'Only pending or failed batches can be cancelled.']);
        }

        if ($importBatch->file_path) {
            Storage::disk('local')->delete($importBatch->file_path);
        }

        $importBatch->delete();

        return redirect()->route('imports.index')
            ->with('success', 'Import batch cancelled.');
    }
}
