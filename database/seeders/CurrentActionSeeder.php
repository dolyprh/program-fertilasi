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
            ['code' => 'HDSP', 'name' => 'HDSP', 'sort_order' => 1],
            ['code' => 'HDSP_PLUS_GA', 'name' => 'HDSP + GA', 'sort_order' => 2],
            ['code' => 'HYPOSY', 'name' => 'HYPOSY', 'sort_order' => 3],
            ['code' => 'KONSUL_ONLY', 'name' => 'Konsul Only', 'sort_order' => 4],
            ['code' => 'KONSUL_USG_FW', 'name' => 'Konsul Usg FW', 'sort_order' => 5],
            ['code' => 'KONTROL_D12_D13_PERTENGAHAN_SIKLUS', 'name' => 'Kontrol D12-D13 (Pertengahan Siklus)', 'sort_order' => 6],
            ['code' => 'KONTROL_D2_HAID', 'name' => 'Kontrol D2 haid', 'sort_order' => 7],
            ['code' => 'KONTROL_D2_D5', 'name' => 'Kontrol D2-D5', 'sort_order' => 8],
            ['code' => 'KONTROL_ONLY', 'name' => 'Kontrol Only', 'sort_order' => 9],
            ['code' => 'KONTROL_POST_HDSP', 'name' => 'Kontrol Post HDSP', 'sort_order' => 10],
            ['code' => 'KONTROL_USG', 'name' => 'Kontrol Usg', 'sort_order' => 11],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_3', 'name' => 'Paket Akupuntur 6x Ke 3', 'sort_order' => 12],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_4', 'name' => 'Paket Akupuntur 6x Ke 4', 'sort_order' => 13],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_5', 'name' => 'Paket Akupuntur 6x Ke 5', 'sort_order' => 14],
            ['code' => 'SA_PLUS_DFI_PLUS_KONTROL', 'name' => 'SA + DFI + Kontrol', 'sort_order' => 15],
            ['code' => 'SA_PLUS_KONTROL', 'name' => 'SA + Kontrol', 'sort_order' => 16],
            ['code' => 'KONTROL_STIMULASI_IUI', 'name' => 'Kontrol Stimulasi IUI', 'sort_order' => 17],
            ['code' => 'ELEKTROAKUPUNTUR', 'name' => 'Elektroakupuntur', 'sort_order' => 18],
            ['code' => 'HD', 'name' => 'HD', 'sort_order' => 19],
            ['code' => 'KONSUL_PLUS_ELEKTROAKUPUNTUR', 'name' => 'Konsul + Elektroakupuntur', 'sort_order' => 20],
            ['code' => 'KONSUL_DAN_USG', 'name' => 'Konsul dan USG', 'sort_order' => 21],
            ['code' => 'KONTROL_PLUS_ELEKTROAKUPUNTUR', 'name' => 'Kontrol + Elektroakupuntur', 'sort_order' => 22],
            ['code' => 'KONTROL_MINI_STIMULASI_IVF', 'name' => 'Kontrol Mini Stimulasi IVF', 'sort_order' => 23],
            ['code' => 'KONTROL_POST_OPU', 'name' => 'Kontrol Post OPU', 'sort_order' => 24],
            ['code' => 'KONTROL_STIMULASI_FET', 'name' => 'Kontrol Stimulasi FET', 'sort_order' => 25],
            ['code' => 'KONTROL_STIMULASI_IVF', 'name' => 'Kontrol Stimulasi IVF', 'sort_order' => 26],
            ['code' => 'KONTROL_DAN_USG', 'name' => 'Kontrol dan USG', 'sort_order' => 27],
            ['code' => 'OPU', 'name' => 'OPU', 'sort_order' => 28],
            ['code' => 'OPU_PLUS_HDSP', 'name' => 'OPU + HDSP', 'sort_order' => 29],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_1', 'name' => 'Paket Akupuntur 12x Ke 1', 'sort_order' => 30],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_11', 'name' => 'Paket Akupuntur 12x Ke 11', 'sort_order' => 31],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_12', 'name' => 'Paket Akupuntur 12x Ke 12', 'sort_order' => 32],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_2', 'name' => 'Paket Akupuntur 12x Ke 2', 'sort_order' => 33],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_3', 'name' => 'Paket Akupuntur 12x Ke 3', 'sort_order' => 34],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_4', 'name' => 'Paket Akupuntur 12x Ke 4', 'sort_order' => 35],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_5', 'name' => 'Paket Akupuntur 12x Ke 5', 'sort_order' => 36],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_6', 'name' => 'Paket Akupuntur 12x Ke 6', 'sort_order' => 37],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_7', 'name' => 'Paket Akupuntur 12x Ke 7', 'sort_order' => 38],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_8', 'name' => 'Paket Akupuntur 12x Ke 8', 'sort_order' => 39],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_1', 'name' => 'Paket Akupuntur 6x Ke 1', 'sort_order' => 40],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_2', 'name' => 'Paket Akupuntur 6x Ke 2', 'sort_order' => 41],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_6', 'name' => 'Paket Akupuntur 6x Ke 6', 'sort_order' => 42],
            ['code' => 'KONSUL_PLUS_SA_PLUS_DFI_NEW_RUJUKAN_DPJP_BI', 'name' => 'Konsul + SA + DFI (New/ Rujukan DPJP BI)', 'sort_order' => 43],
            ['code' => 'KONSUL_FW', 'name' => 'Konsul FW', 'sort_order' => 44],
            ['code' => 'KONSUL_FW_PLUS_SA', 'name' => 'Konsul FW + SA', 'sort_order' => 45],
            ['code' => 'KONSUL_FW_PLUS_SA_PLUS_DFI', 'name' => 'Konsul FW + SA + DFI', 'sort_order' => 46],
            ['code' => 'KONSUL_FW_ONLY', 'name' => 'Konsul FW Only', 'sort_order' => 47],
            ['code' => 'KONSUL_ONLY_NEW_RUJUKAN_DPJP_BI', 'name' => 'Konsul Only (New/ Rujukan DPJP BI)', 'sort_order' => 48],
            ['code' => 'KONSUL_USG_NEW_RUJUKAN_DPJP_BI', 'name' => 'Konsul Usg (New/ Rujukan DPJP BI)', 'sort_order' => 49],
            ['code' => 'SA_PLUS_DFI_PLUS_KONSUL_FW', 'name' => 'SA + DFI + Konsul FW', 'sort_order' => 50],
            ['code' => 'HO', 'name' => 'HO', 'sort_order' => 51],
            ['code' => 'PRP_OVARIUM_TRANSVAGINAL', 'name' => 'PRP Ovarium Transvaginal', 'sort_order' => 52],
            ['code' => 'LAINNYA', 'name' => 'Lainnya', 'sort_order' => 53],
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
