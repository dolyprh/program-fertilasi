<?php

namespace Database\Seeders;

use App\Models\Classification;
use App\Models\CurrentAction;
use App\Models\CurrentPromil;
use App\Models\Doctor;
use App\Models\Poa1Option;
use Illuminate\Database\Seeder;

class CleanupDummyDataSeeder extends Seeder
{
    /**
     * Remove placeholder and factory-generated master data from development.
     */
    public function run(): void
    {
        $dummyDoctorIds = Doctor::query()
            ->whereIn('code', ['D001', 'D002'])
            ->orWhere('name', 'like', 'Dokter Pemeriksa%')
            ->pluck('id');

        $dummyActionIds = CurrentAction::query()
            ->whereRaw("code LIKE 'ACTION_%'")
            ->pluck('id');

        $dummyPromilIds = CurrentPromil::query()
            ->whereRaw("code LIKE 'PROMIL_%'")
            ->pluck('id');

        $dummyPoa1Ids = Poa1Option::query()
            ->where(function ($query): void {
                $query->whereRaw("code LIKE 'POA1_%'")
                    ->orWhere('code', 'BELUM_DIISI');
            })
            ->pluck('id');

        $dummyMasterIds = collect([
            'doctor_id' => $dummyDoctorIds,
            'current_action_id' => $dummyActionIds,
            'current_promil_id' => $dummyPromilIds,
            'poa1_option_id' => $dummyPoa1Ids,
        ]);

        Classification::query()
            ->where(function ($query) use ($dummyMasterIds): void {
                foreach ($dummyMasterIds as $column => $ids) {
                    if ($ids->isNotEmpty()) {
                        $query->orWhereIn($column, $ids);
                    }
                }
            })
            ->delete();

        if ($dummyDoctorIds->isNotEmpty()) {
            Doctor::query()->whereIn('id', $dummyDoctorIds)->delete();
        }

        if ($dummyActionIds->isNotEmpty()) {
            CurrentAction::query()->whereIn('id', $dummyActionIds)->delete();
        }

        if ($dummyPromilIds->isNotEmpty()) {
            CurrentPromil::query()->whereIn('id', $dummyPromilIds)->delete();
        }

        if ($dummyPoa1Ids->isNotEmpty()) {
            Poa1Option::query()->whereIn('id', $dummyPoa1Ids)->delete();
        }
    }
}
