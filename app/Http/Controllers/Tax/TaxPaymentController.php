<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use App\Models\TaxFiling;
use App\Models\TaxPayment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tax.manage');
    }

    public function index(TaxFiling $taxFiling)
    {
        return Inertia::render('Tax/Payments', [
            'filing' => $taxFiling,
            'payments' => $taxFiling->payments()->latest('payment_date')->paginate(50),
        ]);
    }

    public function store(Request $request, TaxFiling $taxFiling)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_reference' => 'nullable|string',
        ]);

        $taxFiling->payments()->create($validated);

        return back()->with('success', 'Payment recorded.');
    }
}
