<?php

namespace App\Repositories;

use App\Models\ReportPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SarangErpRepository
{
    public function taxTransactionsFor(ReportPeriod $period): Collection
    {
        return DB::connection('sarang_erp')
            ->table('tax_transactions')
            ->whereYear('transaction_date', $period->year)
            ->whereMonth('transaction_date', $period->month)
            ->get();
    }

    public function taxReportsFor(ReportPeriod $period): Collection
    {
        return DB::connection('sarang_erp')
            ->table('tax_reports')
            ->where('year', $period->year)
            ->where('month', $period->month)
            ->get();
    }

    public function upcomingTaxFilings(int $daysAhead = 30): Collection
    {
        return DB::connection('sarang_erp')
            ->table('tax_reports')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays($daysAhead)->toDateString()])
            ->orderBy('due_date')
            ->get();
    }
}
