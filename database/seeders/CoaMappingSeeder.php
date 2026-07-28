<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CoaMapping;
use App\Models\PnlLine;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoaMappingSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [
            '400' => 'REVENUE',
            'BACKCHARGE_FUEL' => 'BACKCHARGE_FUEL',
            '51100' => 'COST_FUEL',
            '51100002' => 'COST_FUEL_TRANSPORT',
            '51200' => 'COST_LUBE',
            '51300' => 'COST_RENTAL',
            '51400' => 'COST_BLASTING',
            '51500' => 'COST_SAFETY',
            '51600' => 'COST_LAB',
            '51800' => 'COST_INSURANCE',
            '51900' => 'COST_SPARE_PARTS',
            'EMP_EXP' => 'EMPLOYEE_EXPENSE',
            'ADMIN_SUPPLIES_IT' => 'ADMIN_SUPPLIES_IT',
            'DEPR' => 'DEPRECIATION',
            'OTHER' => 'OTHER',
        ];

        $createdBy = User::where('email', 'admin@arkaledger.test')->value('id');

        foreach ($mappings as $sapCode => $pnlLineCode) {
            $accountId = Account::where('sap_code', $sapCode)->value('id');
            $pnlLineId = PnlLine::where('code', $pnlLineCode)->value('id');

            if (! $accountId || ! $pnlLineId) {
                continue;
            }

            CoaMapping::updateOrCreate(
                [
                    'account_id' => $accountId,
                    'effective_from' => '2024-01-01',
                ],
                [
                    'pnl_line_id' => $pnlLineId,
                    'version' => 1,
                    'created_by' => $createdBy,
                ]
            );
        }
    }
}
