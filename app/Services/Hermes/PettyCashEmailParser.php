<?php

namespace App\Services\Hermes;

use App\Models\EmailTemplate;
use App\Models\PettyCashExpense;
use App\Models\PettyCashFund;
use App\Models\ReportPeriod;
use Carbon\Carbon;

class PettyCashEmailParser
{
    public function parse(array $payload, EmailTemplate $template, ReportPeriod $period, int $siteId): array
    {
        $fund = PettyCashFund::firstOrCreate(
            ['project_site_id' => $siteId, 'report_period_id' => $period->id],
            ['opening_balance' => 0, 'replenishment_amount' => 0, 'closing_balance' => 0]
        );

        $columnMap = $template->column_map;
        $rows = $payload['rows'] ?? $payload['attachments'][0]['rows'] ?? [];
        $created = [];

        foreach ($rows as $row) {
            $expense = PettyCashExpense::create([
                'petty_cash_fund_id' => $fund->id,
                'expense_date' => Carbon::parse($row[$columnMap['expense_date'] ?? 'date'] ?? now()),
                'category' => $row[$columnMap['category'] ?? 'category'] ?? 'General',
                'description' => $row[$columnMap['description'] ?? 'description'] ?? null,
                'amount' => (float) ($row[$columnMap['amount'] ?? 'amount'] ?? 0),
                'source' => 'email_import',
            ]);
            $created[] = $expense;
        }

        return $created;
    }
}
