<?php

use App\Services\DecisionTreeService;

beforeEach(function () {
    $this->service = new DecisionTreeService;
});

test('rules metadata contains seventy eight rules with support totaling 597', function () {
    $rules = $this->service->rules();

    expect($rules)->toHaveCount(78)
        ->and(collect($rules)->sum('support_count'))->toBe(597);
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
    'R1 alami age 30' => [30, 'ALAMI', 'LAINNYA', 'R1', 'PRO IVF'],
    'R9 alami age 29 kontrol d2d5' => [29, 'ALAMI', 'KONTROL_D2_D5', 'R9', 'PRO ST'],
    'R19 belum promil age 30' => [30, 'BELUM_PROMIL', 'LAINNYA', 'R19', 'PRO IUI'],
    'R18 belum promil age 44' => [44, 'BELUM_PROMIL', 'LAINNYA', 'R18', 'PRO Stimulasi IVF'],
    'R21 iui kontrol d2d5' => [35, 'IUI', 'KONTROL_D2_D5', 'R21', 'PRO Stimulasi IUI'],
    'R23 ivf age 44' => [44, 'IVF', 'LAINNYA', 'R23', 'PRO Stimulasi IVF'],
    'R31 ivf age 35 kontrol' => [35, 'IVF', 'KONTROL_D2_D5', 'R31', 'PRO Stimulasi FET'],
    'R68 pemeriksaan konsul usg fw age 30' => [30, 'PEMERIKSAAN_DASAR', 'KONSUL_USG_FW', 'R68', 'PRO IVF'],
    'R76 senggama age 28' => [28, 'SENGGAMA_TERJADWAL', 'LAINNYA', 'R76', 'PRO IUI'],
    'R78 senggama age 26' => [26, 'SENGGAMA_TERJADWAL', 'LAINNYA', 'R78', 'PRO IUI/PRO IVF'],
]);

test('returns no rule for alami young with unmatched action', function () {
    $result = $this->service->classify([
        'age' => 28,
        'current_promil_code' => 'ALAMI',
        'current_action_code' => 'LAINNYA',
    ]);

    expect($result['matched'])->toBeFalse()
        ->and($result['prediction'])->toBeNull()
        ->and($result['rule_code'])->toBeNull()
        ->and($result['confidence'])->toBeNull()
        ->and($result['support_count'])->toBeNull()
        ->and($result['support_percentage'])->toBeNull();
});

test('returns expected confidence for R31 ivf kontrol d2d5', function () {
    $result = $this->service->classify([
        'age' => 35,
        'current_promil_code' => 'IVF',
        'current_action_code' => 'KONTROL_D2_D5',
    ]);

    expect($result['rule_code'])->toBe('R31')
        ->and($result['confidence'])->toBe(78.57)
        ->and($result['support_count'])->toBe(14)
        ->and($result['support_percentage'])->toBe(2.35);
});

test('returns expected support for R1 alami older', function () {
    $result = $this->service->classify([
        'age' => 35,
        'current_promil_code' => 'ALAMI',
        'current_action_code' => 'LAINNYA',
    ]);

    expect($result['rule_code'])->toBe('R1')
        ->and($result['support_count'])->toBe(197)
        ->and($result['support_percentage'])->toBe(33.0);

    $rules = collect($this->service->rules())->firstWhere('code', 'R1');

    expect($rules['support_count'])->toBe(197)
        ->and($rules['support_percentage'])->toBe(33.0);
});
