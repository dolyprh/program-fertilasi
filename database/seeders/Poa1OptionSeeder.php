<?php

namespace Database\Seeders;

use App\Models\Poa1Option;
use Illuminate\Database\Seeder;

class Poa1OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Kategori PoA1 dari nilai unik dataset penelitian.
     */
    public function run(): void
    {
        $items = [
            ['code' => 'PREPARASI_SPERMA', 'name' => 'Preparasi Sperma', 'sort_order' => 1],
            ['code' => 'KONTROL_HAID_D2_D3', 'name' => 'Kontrol di Haid ke D2 – D3', 'sort_order' => 2],
            ['code' => 'KONTROL_STIMULASI_FET', 'name' => 'Kontrol Stimulasi FET', 'sort_order' => 3],
            ['code' => 'KONTROL_USG', 'name' => 'Kontrol Usg', 'sort_order' => 4],
            ['code' => 'KONTROL_PERTENGAHAN_SIKLUS_D12_D13', 'name' => 'Kontrol Pertengahan Siklus (D12-D13)', 'sort_order' => 5],
            ['code' => 'HISTEROSKOPI_DIAGNOSTIK_ANASTESI', 'name' => 'Histeroskopi Diagnostik dengan anastesi', 'sort_order' => 6],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_7', 'name' => 'Paket Akupuntur 12x Ke 7', 'sort_order' => 7],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_8', 'name' => 'Paket Akupuntur 12x Ke 8', 'sort_order' => 8],
            ['code' => 'SA_DFI_KONTROL', 'name' => 'SA + DFI + Kontrol', 'sort_order' => 9],
            ['code' => 'KONTROL', 'name' => 'Kontrol', 'sort_order' => 10],
            ['code' => 'KONTROL_STIMULASI_IUI', 'name' => 'Kontrol Stimulasi IUI', 'sort_order' => 11],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_6', 'name' => 'Paket Akupuntur 12x Ke 6', 'sort_order' => 12],
            ['code' => 'ELEKTROAKUPUNTUR', 'name' => 'Elektroakupuntur', 'sort_order' => 13],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_5', 'name' => 'Paket Akupuntur 6x Ke 5', 'sort_order' => 14],
            ['code' => 'HISTEROSKOPI_DIAGNOSTIK', 'name' => 'Histeroskopi Diagnostik', 'sort_order' => 15],
            ['code' => 'SA_KONTROL', 'name' => 'SA + Kontrol', 'sort_order' => 16],
            ['code' => 'SA_URIN_POST_EJAKULASI_DFI_KONTROL', 'name' => 'SA + Urin Post Ejakulasi + DFI + Kontrol', 'sort_order' => 17],
            ['code' => 'PAKET_AKUPUNTUR_6X_KE_3', 'name' => 'Paket Akupuntur 6x Ke 3', 'sort_order' => 18],
            ['code' => 'PAKET_AKUPUNTUR_12X_KE_10', 'name' => 'Paket Akupuntur 12x Ke 10', 'sort_order' => 19],
            ['code' => 'KONTROL_ELEKTROAKUPUNTUR', 'name' => 'Kontrol + Elektroakupuntur', 'sort_order' => 20],
            ['code' => 'PRP_OVARIUM_TRANSVAGINAL', 'name' => 'PRP Ovarium Transvaginal', 'sort_order' => 21],
            ['code' => 'SA_SPERMA_SKRINING_KONTROL', 'name' => 'SA + Sperma Skrining + Kontrol', 'sort_order' => 22],
            ['code' => 'TESA_PESA_DIAGNOSTIK_SPERM_FREEZING', 'name' => 'TESA PESA Diagnostik + Sperm Freezing', 'sort_order' => 23],
            ['code' => 'SA_DFI_SKRINING_SPERMA_KONTROL', 'name' => 'SA + DFI + Skrining Sperma + Kontrol', 'sort_order' => 24],
            ['code' => 'KONTROL_D2_D5', 'name' => 'Kontrol D2-D5', 'sort_order' => 25],
        ];

        foreach ($items as $item) {
            Poa1Option::query()->updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ],
            );
        }

        Poa1Option::query()
            ->where('code', 'BELUM_DIISI')
            ->update(['is_active' => false]);
    }
}
