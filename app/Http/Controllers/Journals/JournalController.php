<?php

namespace App\Http\Controllers\Journals;

use App\Http\Controllers\Controller;
use App\Http\Requests\Journals\StoreJournalRequest;
use App\Models\Account;
use App\Models\Journal;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JournalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:journals.manage')->except(['approve', 'reject']);
        $this->middleware('permission:journals.approve')->only(['approve', 'reject']);
    }

    public function index(): Response
    {
        return Inertia::render('Journals/Index', [
            'journals' => Journal::with(['reportPeriod', 'projectSite', 'lines'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Journals/Create', [
            'periods' => ReportPeriod::orderByDesc('year')->orderByDesc('month')->get(),
            'sites' => ProjectSite::where('is_active', true)->orderBy('sort_order')->get(),
            'accounts' => Account::orderBy('sap_code')->get(['id', 'sap_code', 'name']),
        ]);
    }

    public function store(StoreJournalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $journal = Journal::create([
            'report_period_id' => $data['report_period_id'],
            'project_site_id' => $data['project_site_id'],
            'reference_no' => $data['reference_no'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'source' => 'manual',
            'created_by' => $request->user()->id,
        ]);

        foreach ($data['lines'] as $index => $line) {
            $journal->lines()->create([
                'account_id' => $line['account_id'],
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
                'memo' => $line['memo'] ?? null,
                'line_order' => $line['line_order'] ?? $index,
            ]);
        }

        return redirect()->route('journals.show', $journal)
            ->with('success', 'Journal entry created.');
    }

    public function show(Journal $journal): Response
    {
        $journal->load(['reportPeriod', 'projectSite', 'lines.account', 'createdBy', 'approvedBy']);

        return Inertia::render('Journals/Show', [
            'journal' => $journal,
            'isBalanced' => $journal->isBalanced(),
        ]);
    }

    public function update(StoreJournalRequest $request, Journal $journal): RedirectResponse
    {
        if (in_array($journal->status, ['approved'], true)) {
            return back()->withErrors(['status' => 'Approved journals cannot be edited.']);
        }

        $data = $request->validated();

        $journal->update([
            'report_period_id' => $data['report_period_id'],
            'project_site_id' => $data['project_site_id'],
            'reference_no' => $data['reference_no'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? $journal->status,
        ]);

        $journal->lines()->delete();

        foreach ($data['lines'] as $index => $line) {
            $journal->lines()->create([
                'account_id' => $line['account_id'],
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
                'memo' => $line['memo'] ?? null,
                'line_order' => $line['line_order'] ?? $index,
            ]);
        }

        return redirect()->route('journals.show', $journal)
            ->with('success', 'Journal entry updated.');
    }

    public function approve(Request $request, Journal $journal): RedirectResponse
    {
        if (! $journal->isBalanced()) {
            return back()->withErrors(['approve' => 'Cannot approve an unbalanced journal entry.']);
        }

        $journal->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Journal entry approved.');
    }

    public function reject(Request $request, Journal $journal): RedirectResponse
    {
        $request->validate(['comments' => ['required', 'string', 'max:1000']]);

        $journal->update([
            'status' => 'rejected',
            'description' => trim(($journal->description ?? '')."\n\nRejection: ".$request->comments),
        ]);

        return back()->with('success', 'Journal entry rejected.');
    }
}
