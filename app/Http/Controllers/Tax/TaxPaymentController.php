<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\StoreTaxPaymentRequest;
use App\Models\TaxFiling;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaxPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tax.manage');
    }

    public function index(TaxFiling $taxFiling): Response
    {
        $taxFiling->load(['projectSite', 'reportPeriod']);

        return Inertia::render('Tax/Payments', [
            'filing' => $taxFiling,
            'payments' => $taxFiling->payments()
                ->orderByDesc('payment_date')
                ->paginate(30),
        ]);
    }

    public function store(StoreTaxPaymentRequest $request, TaxFiling $taxFiling): RedirectResponse
    {
        $taxFiling->payments()->create($request->validated());

        return back()->with('success', 'Payment recorded.');
    }
}
