<?php

namespace Database\Seeders;

use App\Models\PnlLine;
use Illuminate\Database\Seeder;

class PnlLineSeeder extends Seeder
{
    public function run(): void
    {
        $lines = [
            ['code' => 'ROOT', 'name' => 'Root', 'parent' => null, 'sign' => 1, 'is_subtotal' => false, 'sort_order' => 0],
            ['code' => 'REVENUE_ENGINEERING', 'name' => 'Revenue Engineering', 'parent' => 'ROOT', 'sign' => 1, 'is_subtotal' => false, 'sort_order' => 10],
            ['code' => 'REVENUE', 'name' => 'Revenue', 'parent' => 'REVENUE_ENGINEERING', 'sign' => 1, 'is_subtotal' => false, 'sort_order' => 20],
            ['code' => 'BACKCHARGE_FUEL', 'name' => 'Backcharge Fuel', 'parent' => 'REVENUE_ENGINEERING', 'sign' => 1, 'is_subtotal' => false, 'sort_order' => 30],
            ['code' => 'COST_IPH', 'name' => 'Cost IPH', 'parent' => 'ROOT', 'sign' => -1, 'is_subtotal' => true, 'sort_order' => 40],
            ['code' => 'COST_OF_SALES', 'name' => 'Cost of Sales', 'parent' => 'COST_IPH', 'sign' => -1, 'is_subtotal' => true, 'sort_order' => 50],
            ['code' => 'COST_FUEL', 'name' => 'Fuel', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 60],
            ['code' => 'COST_FUEL_TRANSPORT', 'name' => 'Fuel Transportation', 'parent' => 'COST_FUEL', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 70],
            ['code' => 'COST_LUBE', 'name' => 'Lube', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 80],
            ['code' => 'COST_RENTAL', 'name' => 'Rental', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 90],
            ['code' => 'COST_BLASTING', 'name' => 'Blasting', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 100],
            ['code' => 'COST_SAFETY', 'name' => 'Safety', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 110],
            ['code' => 'COST_LAB', 'name' => 'Lab', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 120],
            ['code' => 'COST_INSURANCE', 'name' => 'Insurance', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 130],
            ['code' => 'COST_SPARE_PARTS', 'name' => 'Spare Parts', 'parent' => 'COST_OF_SALES', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 140],
            ['code' => 'EMPLOYEE_EXPENSE', 'name' => 'Employee Expense', 'parent' => 'ROOT', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 150],
            ['code' => 'ADMIN_SUPPLIES_IT', 'name' => 'Admin / Supplies / IT', 'parent' => 'ROOT', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 160],
            ['code' => 'DEPRECIATION', 'name' => 'Depreciation', 'parent' => 'ROOT', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 170],
            ['code' => 'OTHER', 'name' => 'Other', 'parent' => 'ROOT', 'sign' => -1, 'is_subtotal' => false, 'sort_order' => 180],
            ['code' => 'PROFIT_LOSS', 'name' => 'Profit / Loss', 'parent' => 'ROOT', 'sign' => 1, 'is_subtotal' => true, 'sort_order' => 190],
        ];

        $codeToId = [];

        foreach ($lines as $line) {
            $parentId = $line['parent'] ? ($codeToId[$line['parent']] ?? null) : null;

            $record = PnlLine::updateOrCreate(
                ['code' => $line['code']],
                [
                    'name' => $line['name'],
                    'parent_id' => $parentId,
                    'sign' => $line['sign'],
                    'is_subtotal' => $line['is_subtotal'],
                    'sort_order' => $line['sort_order'],
                ]
            );

            $codeToId[$line['code']] = $record->id;
        }
    }
}
