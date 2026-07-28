<?php

namespace Database\Seeders;

use App\Models\ProjectSite;
use Illuminate\Database\Seeder;

class ProjectSiteSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            ['code' => '017C', 'name' => 'Coal Mining Site', 'type' => 'mining', 'sort_order' => 10],
            ['code' => '021C', 'name' => 'Limestone & Shalestone Mining', 'type' => 'quarry', 'sort_order' => 20],
            ['code' => '022C', 'name' => 'Mining Site', 'type' => 'mining', 'sort_order' => 30],
            ['code' => '023C', 'name' => 'Mining Site', 'type' => 'mining', 'sort_order' => 40],
            ['code' => '025C', 'name' => 'Mining Site', 'type' => 'mining', 'sort_order' => 50],
            ['code' => '026C', 'name' => 'New Mining Site (2025)', 'type' => 'mining', 'sort_order' => 60],
            ['code' => 'APS', 'name' => 'APS Business Unit (CHO)', 'type' => 'services', 'sort_order' => 70],
            ['code' => 'HO', 'name' => 'Head Office — Balikpapan', 'type' => 'admin', 'sort_order' => 80],
            ['code' => 'JKT', 'name' => 'Jakarta Office', 'type' => 'admin', 'sort_order' => 90],
        ];

        foreach ($sites as $site) {
            ProjectSite::updateOrCreate(
                ['code' => $site['code']],
                [
                    'name' => $site['name'],
                    'type' => $site['type'],
                    'is_active' => true,
                    'sort_order' => $site['sort_order'],
                ]
            );
        }
    }
}
