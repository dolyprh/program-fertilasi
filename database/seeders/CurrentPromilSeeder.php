<?php

namespace Database\Seeders;

use App\Models\CurrentPromil;
use Illuminate\Database\Seeder;

class CurrentPromilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['code' => 'ALAMI', 'name' => 'Alami', 'sort_order' => 1],
            ['code' => 'BELUM_PROMIL', 'name' => 'Belum Promil', 'sort_order' => 2],
            ['code' => 'IUI', 'name' => 'IUI', 'sort_order' => 3],
            ['code' => 'IVF', 'name' => 'IVF', 'sort_order' => 4],
            ['code' => 'PEMERIKSAAN_DASAR', 'name' => 'Pemeriksaan Dasar', 'sort_order' => 5],
            ['code' => 'SENGGAMA_TERJADWAL', 'name' => 'Senggama Terjadwal', 'sort_order' => 6],
        ];

        foreach ($items as $item) {
            CurrentPromil::query()->updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ],
            );
        }
    }
}
