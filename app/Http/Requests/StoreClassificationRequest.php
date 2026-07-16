<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_name' => ['required', 'string', 'max:255'],
            'visit_date' => ['required', 'date'],
            'date_of_birth' => [
                'required',
                'date',
                'before_or_equal:visit_date',
            ],
            'doctor_id' => [
                'required',
                Rule::exists('doctors', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'current_promil_id' => [
                'required',
                Rule::exists('current_promils', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'current_action_id' => [
                'required',
                Rule::exists('current_actions', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'poa1_option_id' => [
                'required',
                Rule::exists('poa1_options', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'patient_name' => 'nama lengkap',
            'visit_date' => 'tanggal kunjungan',
            'date_of_birth' => 'tanggal lahir',
            'doctor_id' => 'dokter pemeriksa',
            'current_promil_id' => 'promil saat ini',
            'current_action_id' => 'tindakan hari ini',
            'poa1_option_id' => 'PoA1',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'Tanggal lahir tidak boleh melebihi tanggal kunjungan.',
        ];
    }
}
