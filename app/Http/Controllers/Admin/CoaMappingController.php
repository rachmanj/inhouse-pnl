<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCoaMappingRequest;
use App\Models\Account;
use App\Models\CoaMapping;
use App\Models\PnlLine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CoaMappingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:coa-mappings.manage');
    }

    public function index(): Response
    {
        return Inertia::render('Admin/CoaMappings/Index', [
            'mappings' => CoaMapping::with(['account', 'pnlLine'])->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/CoaMappings/Form', [
            'mapping' => null,
            'accounts' => Account::orderBy('sap_code')->get(['id', 'sap_code', 'name']),
            'pnlLines' => PnlLine::orderBy('sort_order')->get(['id', 'code', 'name']),
        ]);
    }

    public function store(StoreCoaMappingRequest $request): RedirectResponse
    {
        CoaMapping::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.coa-mappings.index')
            ->with('success', 'CoA mapping created.');
    }

    public function edit(CoaMapping $coaMapping): Response
    {
        return Inertia::render('Admin/CoaMappings/Form', [
            'mapping' => $coaMapping->load(['account', 'pnlLine']),
            'accounts' => Account::orderBy('sap_code')->get(['id', 'sap_code', 'name']),
            'pnlLines' => PnlLine::orderBy('sort_order')->get(['id', 'code', 'name']),
        ]);
    }

    public function update(StoreCoaMappingRequest $request, CoaMapping $coaMapping): RedirectResponse
    {
        $coaMapping->update($request->validated());

        return redirect()->route('admin.coa-mappings.index')
            ->with('success', 'CoA mapping updated.');
    }

    public function destroy(CoaMapping $coaMapping): RedirectResponse
    {
        $coaMapping->delete();

        return redirect()->route('admin.coa-mappings.index')
            ->with('success', 'CoA mapping deleted.');
    }

    public function simulate(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'pnl_line_id' => ['required', 'exists:pnl_lines,id'],
        ]);

        $account = Account::find($validated['account_id']);
        $pnlLine = PnlLine::with('parent')->find($validated['pnl_line_id']);

        return response()->json([
            'account' => $account,
            'pnl_line' => $pnlLine,
            'preview' => [
                'message' => "Account {$account->sap_code} would map to P&L line {$pnlLine->code}",
            ],
        ]);
    }
}
