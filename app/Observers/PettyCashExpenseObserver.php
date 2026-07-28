<?php

namespace App\Observers;

use App\Models\PettyCashExpense;
use App\Models\PettyCashFund;

class PettyCashExpenseObserver
{
    public function saved(PettyCashExpense $expense): void
    {
        $this->recalculateClosingBalance($expense->petty_cash_fund_id);
    }

    public function deleted(PettyCashExpense $expense): void
    {
        $this->recalculateClosingBalance($expense->petty_cash_fund_id);
    }

    private function recalculateClosingBalance(int $fundId): void
    {
        $fund = PettyCashFund::withSum('expenses', 'amount')->find($fundId);

        if (! $fund) {
            return;
        }

        $totalExpenses = (float) ($fund->expenses_sum_amount ?? 0);
        $closing = (float) $fund->opening_balance
            + (float) $fund->replenishment_amount
            - $totalExpenses;

        $fund->update(['closing_balance' => $closing]);
    }
}
