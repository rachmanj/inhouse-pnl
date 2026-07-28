<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccountRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:accounts.manage');
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Accounts/Index', [
            'accounts' => Account::with('parent')->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Accounts/Form', [
            'account' => null,
            'parents' => Account::orderBy('sap_code')->get(['id', 'sap_code', 'name']),
        ]);
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        Account::create($request->validated());

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Account created.');
    }

    public function edit(Account $account): Response
    {
        return Inertia::render('Admin/Accounts/Form', [
            'account' => $account,
            'parents' => Account::where('id', '!=', $account->id)->orderBy('sap_code')->get(['id', 'sap_code', 'name']),
        ]);
    }

    public function update(StoreAccountRequest $request, Account $account): RedirectResponse
    {
        $account->update($request->validated());

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Account updated.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $account->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Account deleted.');
    }
}
