<?php

namespace Database\Factories;

use App\Enums\ClassificationStatus;
use App\Models\Classification;
use App\Models\CurrentAction;
use App\Models\CurrentPromil;
use App\Models\Doctor;
use App\Models\Poa1Option;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Classification>
 */
class ClassificationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $visitDate = fake()->dateTimeBetween('-1 year', 'now');
        $dateOfBirth = fake()->dateTimeBetween('-55 years', '-26 years');
        $age = (int) $dateOfBirth->diff($visitDate)->y;

        return [
            'user_id' => User::factory(),
            'doctor_id' => Doctor::factory(),
            'current_promil_id' => CurrentPromil::factory(),
            'current_action_id' => CurrentAction::factory(),
            'poa1_option_id' => Poa1Option::factory(),
            'case_code' => 'CLS-'.now()->format('Ymd').'-'.Str::ulid(),
            'patient_name' => fake()->name(),
            'visit_date' => $visitDate,
            'date_of_birth' => $dateOfBirth,
            'age' => $age,
            'prediction_result' => 'PRO IVF',
            'rule_code' => 'R1',
            'decision_path' => [
                'Promil Saat Ini = ALAMI',
                'Umur > 28,5',
                'Promil Selanjutnya = PRO IVF',
            ],
            'confidence' => 40.48,
            'support_count' => 42,
            'support_percentage' => 41.58,
            'status' => ClassificationStatus::Classified,
        ];
    }

    public function noRule(): static
    {
        return $this->state(fn (array $attributes) => [
            'prediction_result' => null,
            'rule_code' => null,
            'decision_path' => [
                'Tidak ditemukan decision rule yang sesuai',
            ],
            'confidence' => null,
            'support_count' => null,
            'support_percentage' => null,
            'status' => ClassificationStatus::NoRule,
        ]);
    }
}
