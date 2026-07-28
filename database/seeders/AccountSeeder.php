<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['sap_code' => '400', 'name' => 'Revenue Invoices', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'level' => 0, 'parent' => null],
            ['sap_code' => 'BACKCHARGE_FUEL', 'name' => 'Backcharge Fuel', 'account_type' => 'backcharge', 'normal_balance' => 'credit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51100', 'name' => 'Fuel', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51100002', 'name' => 'Fuel Transportation', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 1, 'parent' => '51100'],
            ['sap_code' => '51200', 'name' => 'Lube', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51300', 'name' => 'Rental', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51400', 'name' => 'Blasting', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51500', 'name' => 'Safety', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51600', 'name' => 'Lab', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51800', 'name' => 'Insurance', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => '51900', 'name' => 'Spare Parts', 'account_type' => 'cost_of_sales', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => 'EMP_EXP', 'name' => 'Employee Expense', 'account_type' => 'employee_expense', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => 'ADMIN_SUPPLIES_IT', 'name' => 'Admin / Supplies / IT', 'account_type' => 'admin_expense', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => 'DEPR', 'name' => 'Depreciation', 'account_type' => 'depreciation', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
            ['sap_code' => 'ACCUM_DEPR', 'name' => 'Accumulated Depreciation', 'account_type' => 'depreciation', 'normal_balance' => 'credit', 'level' => 0, 'parent' => null],
            ['sap_code' => 'OTHER', 'name' => 'Other Income/Expense', 'account_type' => 'other', 'normal_balance' => 'debit', 'level' => 0, 'parent' => null],
        ];

        $codeToId = [];

        foreach ($accounts as $index => $account) {
            $parentId = $account['parent'] ? ($codeToId[$account['parent']] ?? null) : null;

            $record = Account::updateOrCreate(
                ['sap_code' => $account['sap_code']],
                [
                    'name' => $account['name'],
                    'parent_id' => $parentId,
                    'account_type' => $account['account_type'],
                    'normal_balance' => $account['normal_balance'],
                    'level' => $account['level'],
                    'is_postable' => true,
                    'sort_order' => ($index + 1) * 10,
                ]
            );

            $codeToId[$account['sap_code']] = $record->id;
        }
    }
}
