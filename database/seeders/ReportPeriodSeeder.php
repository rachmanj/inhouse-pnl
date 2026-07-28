<?php

namespace Database\Seeders;

use App\Models\ReportPeriod;
use Illuminate\Database\Seeder;

class ReportPeriodSeeder extends Seeder
{
    public function run(): void
    {
        for ($month = 1; $month <= 12; $month++) {
            ReportPeriod::updateOrCreate(
                ['year' => 2025, 'month' => $month],
                ['status' => $month <= 6 ? 'open' : 'open', 'baseline_year' => 2024]
            );
        }
    }
}
