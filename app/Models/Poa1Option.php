<?php

namespace App\Models;

use App\Models\Concerns\HasMasterScopes;
use Database\Factories\Poa1OptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $is_active
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['code', 'name', 'is_active', 'sort_order'])]
class Poa1Option extends Model
{
    /** @use HasFactory<Poa1OptionFactory> */
    use HasFactory, HasMasterScopes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Classification, $this>
     */
    public function classifications(): HasMany
    {
        return $this->hasMany(Classification::class);
    }
}
