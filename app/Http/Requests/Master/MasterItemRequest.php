<?php

namespace App\Http\Requests\Master;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MasterItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => filter_var($this->input('is_active', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->route('type');
        $table = $this->tableForType($type);
        $masterId = $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'code')->ignore($masterId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'kode',
            'name' => 'nama',
            'is_active' => 'status aktif',
            'sort_order' => 'urutan',
        ];
    }

    private function tableForType(?string $type): string
    {
        return match ($type) {
            'doctors' => 'doctors',
            'current-promils' => 'current_promils',
            'current-actions' => 'current_actions',
            'poa1-options' => 'poa1_options',
            default => abort(404),
        };
    }
}
