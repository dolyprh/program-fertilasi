<?php

namespace Database\Seeders;

use App\Models\CurrentAction;
use Illuminate\Database\Seeder;

class CurrentActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['code' => 'HO', 'name' => 'HO', 'sort_order' => 1],
            ['code' => 'KONTROL_D2_D5', 'name' => 'Kontrol D2-D5', 'sort_order' => 2],
            ['code' => 'PRP_OVARIUM_TRANSVAGINAL', 'name' => 'PRP Ovarium Transvaginal', 'sort_order' => 3],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_6', 'name' => 'Paket Akupuntur 12x Ke 6', 'sort_order' => 4],
            ['code' => 'LAINNYA', 'name' => 'Lainnya', 'sort_order' => 5],
        ];

        foreach ($items as $item) {
            CurrentAction::query()->updateOrCreate(
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
