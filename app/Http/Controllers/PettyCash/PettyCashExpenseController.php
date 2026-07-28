<?php

namespace App\Http\Controllers\PettyCash;

use App\Http\Controllers\Controller;
use App\Http\Requests\PettyCash\StorePettyCashExpenseRequest;
use App\Models\PettyCashExpense;
use App\Models\PettyCashFund;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PettyCashExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pettycash.manage');
    }

    public function index(PettyCashFund $pettyCashFund): Response
    {
        $pettyCashFund->load(['projectSite', 'reportPeriod']);

        return Inertia::render('PettyCash/Expenses', [
            'fund' => $pettyCashFund,
            'expenses' => $pettyCashFund->expenses()
                ->with('importBatch')
                ->when(request('category'), fn ($q, $cat) => $q->where('category', $cat))
                ->when(request('from'), fn ($q, $from) => $q->where('expense_date', '>=', $from))
                ->when(request('to'), fn ($q, $to) => $q->where('expense_date', '<=', $to))
                ->orderByDesc('expense_date')
                ->paginate(30)
                ->withQueryString(),
            'filters' => request()->only(['category', 'from', 'to']),
        ]);
    }

    public function store(StorePettyCashExpenseRequest $request, PettyCashFund $pettyCashFund): RedirectResponse
    {
        $pettyCashFund->expenses()->create([
            ...$request->validated(),
            'source' => 'manual',
        ]);

        return back()->with('success', 'Expense recorded.');
    }

    public function destroy(PettyCashExpense $pettyCashExpense): RedirectResponse
    {
        $fundId = $pettyCashExpense->petty_cash_fund_id;
        $pettyCashExpense->delete();

        return redirect()->route('petty-cash.expenses.index', $fundId)
            ->with('success', 'Expense deleted.');
    }
}
