<?php

use App\Enums\ClassificationStatus;
use App\Models\Classification;
use App\Models\CurrentAction;
use App\Models\CurrentPromil;
use App\Models\Doctor;
use App\Models\Poa1Option;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function createClassificationMasters(): array
{
    return [
        'doctor' => Doctor::factory()->create(['code' => 'D100', 'name' => 'Dokter Test']),
        'promil' => CurrentPromil::factory()->create(['code' => 'IVF', 'name' => 'IVF']),
        'action' => CurrentAction::factory()->create(['code' => 'KONTROL_D2_D5', 'name' => 'Kontrol D2-D5']),
        'poa1' => Poa1Option::factory()->create(['code' => 'POA1_TEST', 'name' => 'PoA1 Test']),
    ];
}

test('guests cannot access classification pages', function () {
    $this->get(route('classifications.index'))->assertRedirect(route('login'));
    $this->get(route('classifications.create'))->assertRedirect(route('login'));
});

test('authenticated users can view create classification page', function () {
    $user = User::factory()->create();
    $masters = createClassificationMasters();

    $this->actingAs($user)
        ->get(route('classifications.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('classifications/Create')
            ->has('doctors', 1)
            ->has('currentPromils', 1)
            ->has('currentActions', 1)
            ->has('poa1Options', 1)
            ->where('doctors.0.id', $masters['doctor']->id)
        );
});

test('store calculates age from visit date and applies decision rule', function () {
    $user = User::factory()->create();
    $masters = createClassificationMasters();

    $response = $this->actingAs($user)->post(route('classifications.store'), [
        'patient_name' => 'Pasien Uji',
        'visit_date' => '2026-01-05',
        'date_of_birth' => '1990-01-10',
        'doctor_id' => $masters['doctor']->id,
        'current_promil_id' => $masters['promil']->id,
        'current_action_id' => $masters['action']->id,
        'poa1_option_id' => $masters['poa1']->id,
    ]);

    $classification = Classification::query()->first();

    expect($classification)->not->toBeNull()
        ->and($classification->age)->toBe(35)
        ->and($classification->prediction_result)->toBe('PRO Stimulasi FET')
        ->and($classification->rule_code)->toBe('R6')
        ->and($classification->status)->toBe(ClassificationStatus::Classified)
        ->and($classification->case_code)->toStartWith('CLS-20260105-')
        ->and($classification->user_id)->toBe($user->id);

    $response->assertRedirect(route('classifications.show', $classification));
});

test('store ignores client provided age and prediction fields', function () {
    $user = User::factory()->create();
    $masters = createClassificationMasters();

    $this->actingAs($user)->post(route('classifications.store'), [
        'patient_name' => 'Pasien Uji',
        'visit_date' => '2026-01-05',
        'date_of_birth' => '1990-01-10',
        'doctor_id' => $masters['doctor']->id,
        'current_promil_id' => $masters['promil']->id,
        'current_action_id' => $masters['action']->id,
        'poa1_option_id' => $masters['poa1']->id,
        'age' => 99,
        'prediction_result' => 'FAKE',
        'rule_code' => 'RX',
        'status' => 'classified',
        'case_code' => 'CLS-FAKE',
    ]);

    $classification = Classification::query()->first();

    expect($classification->age)->toBe(35)
        ->and($classification->prediction_result)->toBe('PRO Stimulasi FET')
        ->and($classification->rule_code)->toBe('R6')
        ->and($classification->case_code)->not->toBe('CLS-FAKE');
});

test('store marks no_rule status when no decision rule matches', function () {
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create();
    $promil = CurrentPromil::factory()->create(['code' => 'IVF']);
    $action = CurrentAction::factory()->create(['code' => 'LAINNYA']);
    $poa1 = Poa1Option::factory()->create();

    $this->actingAs($user)->post(route('classifications.store'), [
        'patient_name' => 'Pasien No Rule',
        'visit_date' => '2026-06-01',
        'date_of_birth' => '1980-01-01',
        'doctor_id' => $doctor->id,
        'current_promil_id' => $promil->id,
        'current_action_id' => $action->id,
        'poa1_option_id' => $poa1->id,
    ]);

    $classification = Classification::query()->first();

    expect($classification->status)->toBe(ClassificationStatus::NoRule)
        ->and($classification->prediction_result)->toBeNull()
        ->and($classification->rule_code)->toBeNull();
});

test('users can only view their own classification details', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $classification = Classification::factory()->for($owner)->create();

    $this->actingAs($other)
        ->get(route('classifications.show', $classification))
        ->assertForbidden();

    $this->actingAs($owner)
        ->get(route('classifications.show', $classification))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('classifications/Show')
            ->where('classification.id', $classification->id)
        );
});

test('index lists only the authenticated users classifications and supports filters', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $promil = CurrentPromil::factory()->create(['code' => 'ALAMI', 'name' => 'Alami']);

    $mine = Classification::factory()->for($user)->create([
        'current_promil_id' => $promil->id,
        'case_code' => 'CLS-20260101-TESTMINE1',
        'prediction_result' => 'PRO IVF',
        'status' => ClassificationStatus::Classified,
    ]);

    Classification::factory()->for($other)->create([
        'case_code' => 'CLS-20260101-TESTOTHER',
    ]);

    $this->actingAs($user)
        ->get(route('classifications.index', [
            'case_code' => 'TESTMINE',
            'current_promil_id' => $promil->id,
            'prediction_result' => 'PRO IVF',
            'status' => 'classified',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('classifications/Index')
            ->has('classifications.data', 1)
            ->where('classifications.data.0.id', $mine->id)
        );
});

test('validation rejects date of birth after visit date and inactive masters', function () {
    $user = User::factory()->create();
    $doctor = Doctor::factory()->inactive()->create();
    $promil = CurrentPromil::factory()->create();
    $action = CurrentAction::factory()->create();
    $poa1 = Poa1Option::factory()->create();

    $this->actingAs($user)
        ->post(route('classifications.store'), [
            'patient_name' => 'Pasien Invalid',
            'visit_date' => '2026-01-01',
            'date_of_birth' => '2026-02-01',
            'doctor_id' => $doctor->id,
            'current_promil_id' => $promil->id,
            'current_action_id' => $action->id,
            'poa1_option_id' => $poa1->id,
        ])
        ->assertSessionHasErrors(['date_of_birth', 'doctor_id']);
});
