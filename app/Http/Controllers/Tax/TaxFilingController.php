<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use App\Models\TaxFiling;
use Inertia\Inertia;

class TaxFilingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tax.manage');
    }

    public function index()
    {
        return Inertia::render('Tax/Index', [
            'filings' => TaxFiling::with('projectSite')->latest()->paginate(20),
        ]);
    }

    public function calendar()
    {
        return Inertia::render('Tax/Calendar', [
            'filings' => TaxFiling::orderBy('due_date')->get(),
        ]);
    }
}
