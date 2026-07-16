<?php

use App\Services\DecisionTreeService;

beforeEach(function () {
    $this->service = new DecisionTreeService;
});

test('rules metadata contains fourteen rules with support totaling 101', function () {
    $rules = $this->service->rules();

    expect($rules)->toHaveCount(14)
        ->and(collect($rules)->sum('support_count'))->toBe(101);
});

test('classifies according to decision rules', function (
    int $age,
    string $promil,
    string $action,
    string $ruleCode,
    string $prediction,
) {
    $result = $this->service->classify([
        'age' => $age,
        'current_promil_code' => $promil,
        'current_action_code' => $action,
    ]);

    expect($result['matched'])->toBeTrue()
        ->and($result['rule_code'])->toBe($ruleCode)
        ->and($result['prediction'])->toBe($prediction);
})->with([
    'R1 age 29 alami' => [29, 'ALAMI', 'LAINNYA', 'R1', 'PRO IVF'],
    'R2 age 28 alami' => [28, 'ALAMI', 'LAINNYA', 'R2', 'PRO Stimulasi IUI'],
    'R3 age 27 alami' => [27, 'ALAMI', 'LAINNYA', 'R3', 'PRO ST'],
    'R4 belum promil' => [35, 'BELUM_PROMIL', 'LAINNYA', 'R4', 'PRO IUI/PRO IVF'],
    'R5 iui' => [35, 'IUI', 'LAINNYA', 'R5', 'PRO IUI'],
    'R6 ivf age 40' => [40, 'IVF', 'LAINNYA', 'R6', 'PRO Stimulasi FET'],
    'R7 ivf age 41 ho' => [41, 'IVF', 'HO', 'R7', 'PRO Stimulasi FET'],
    'R8 ivf age 41 kontrol' => [41, 'IVF', 'KONTROL_D2_D5', 'R8', 'PRO Stimulasi FET'],
    'R9 ivf age 41 prp' => [41, 'IVF', 'PRP_OVARIUM_TRANSVAGINAL', 'R9', 'PRO Stimulasi IVF'],
    'R10 ivf age 41 akupuntur' => [41, 'IVF', 'PAKET_AKUPUNTUR_12X_KE_6', 'R10', 'PRO Stimulasi FET'],
    'R11 pemeriksaan dasar 47' => [47, 'PEMERIKSAAN_DASAR', 'LAINNYA', 'R11', 'PRO IUI'],
    'R12 pemeriksaan dasar 46' => [46, 'PEMERIKSAAN_DASAR', 'LAINNYA', 'R12', 'PRO IVF'],
    'R13 pemeriksaan dasar 28' => [28, 'PEMERIKSAAN_DASAR', 'LAINNYA', 'R13', 'PRO IUI'],
    'R14 senggama terjadwal' => [35, 'SENGGAMA_TERJADWAL', 'LAINNYA', 'R14', 'PRO IUI'],
]);

test('returns no rule for ivf age 41 with unmatched action', function () {
    $result = $this->service->classify([
        'age' => 41,
        'current_promil_code' => 'IVF',
        'current_action_code' => 'LAINNYA',
    ]);

    expect($result['matched'])->toBeFalse()
        ->and($result['prediction'])->toBeNull()
        ->and($result['rule_code'])->toBeNull()
        ->and($result['confidence'])->toBeNull()
        ->and($result['support_count'])->toBeNull()
        ->and($result['support_percentage'])->toBeNull();
});

test('returns expected confidence for R6', function () {
    $result = $this->service->classify([
        'age' => 35,
        'current_promil_code' => 'IVF',
        'current_action_code' => 'KONTROL_D2_D5',
    ]);

    expect($result['rule_code'])->toBe('R6')
        ->and($result['confidence'])->toBe(95.83)
        ->and($result['support_count'])->toBe(24)
        ->and($result['support_percentage'])->toBe(23.76);
});

test('returns expected support for R3', function () {
    $result = $this->service->classify([
        'age' => 27,
        'current_promil_code' => 'ALAMI',
        'current_action_code' => 'LAINNYA',
    ]);

    expect($result['rule_code'])->toBe('R3')
        ->and($result['support_count'])->toBe(3)
        ->and($result['support_percentage'])->toBe(2.97);

    $rules = collect($this->service->rules())->firstWhere('code', 'R3');

    expect($rules['support_count'])->toBe(3)
        ->and($rules['support_percentage'])->toBe(2.97);
});
