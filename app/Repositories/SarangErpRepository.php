<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SarangErpRepository
{
    public function taxTransactionsFor(int $year, int $month): array
    {
        try {
            return DB::connection('sarang_erp')
                ->table('tax_transactions')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->get()
                ->toArray();
        } catch (\Throwable) {
            return [];
        }
    }
}
