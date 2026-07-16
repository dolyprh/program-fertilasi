<?php

namespace App\Models;

use App\Enums\ClassificationStatus;
use Database\Factories\ClassificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $doctor_id
 * @property int $current_promil_id
 * @property int $current_action_id
 * @property int $poa1_option_id
 * @property string $case_code
 * @property string $patient_name
 * @property Carbon $visit_date
 * @property Carbon $date_of_birth
 * @property int $age
 * @property string|null $prediction_result
 * @property string|null $rule_code
 * @property array<int, string>|null $decision_path
 * @property string|null $confidence
 * @property int|null $support_count
 * @property string|null $support_percentage
 * @property ClassificationStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Doctor $doctor
 * @property-read CurrentPromil $currentPromil
 * @property-read CurrentAction $currentAction
 * @property-read Poa1Option $poa1Option
 */
#[Fillable([
    'user_id',
    'doctor_id',
    'current_promil_id',
    'current_action_id',
    'poa1_option_id',
    'case_code',
    'patient_name',
    'visit_date',
    'date_of_birth',
    'age',
    'prediction_result',
    'rule_code',
    'decision_path',
    'confidence',
    'support_count',
    'support_percentage',
    'status',
])]
class Classification extends Model
{
    /** @use HasFactory<ClassificationFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'date_of_birth' => 'date',
            'decision_path' => 'array',
            'confidence' => 'decimal:2',
            'support_percentage' => 'decimal:2',
            'status' => ClassificationStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * @return BelongsTo<CurrentPromil, $this>
     */
    public function currentPromil(): BelongsTo
    {
        return $this->belongsTo(CurrentPromil::class);
    }

    /**
     * @return BelongsTo<CurrentAction, $this>
     */
    public function currentAction(): BelongsTo
    {
        return $this->belongsTo(CurrentAction::class);
    }

    /**
     * @return BelongsTo<Poa1Option, $this>
     */
    public function poa1Option(): BelongsTo
    {
        return $this->belongsTo(Poa1Option::class);
    }
}
