<?php

use App\Models\Classification;
use App\Models\CurrentAction;
use App\Models\Doctor;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests cannot access master data pages', function () {
    $this->get(route('master-data.manage', 'doctors'))->assertRedirect(route('login'));
});

test('master data index redirects to doctors page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/master-data')
        ->assertRedirect(route('master-data.manage', 'doctors'));
});

test('authenticated users can manage doctors master data', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('master-data.manage', 'doctors'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('master-data/Manage')
            ->where('type', 'doctors')
            ->where('label', 'Dokter Pemeriksa')
        );
});

test('authenticated users can manage each master data page separately', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    foreach (['doctors', 'current-promils', 'current-actions', 'poa1-options'] as $type) {
        $this->get(route('master-data.manage', $type))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('master-data/Manage')
                ->where('type', $type)
            );
    }
});

test('users can create update and delete master data items', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('master-data.store', 'current-actions'), [
        'code' => 'TEST_ACTION',
        'name' => 'Tindakan Uji',
        'sort_order' => 10,
        'is_active' => true,
    ])->assertRedirect(route('master-data.manage', 'current-actions'));

    $item = CurrentAction::query()->where('code', 'TEST_ACTION')->first();
    expect($item)->not->toBeNull();

    $this->put(route('master-data.update', ['type' => 'current-actions', 'id' => $item->id]), [
        'code' => 'TEST_ACTION',
        'name' => 'Tindakan Diperbarui',
        'sort_order' => 11,
        'is_active' => false,
    ])->assertRedirect(route('master-data.manage', 'current-actions'));

    expect($item->fresh())
        ->name->toBe('Tindakan Diperbarui')
        ->is_active->toBeFalse();

    $this->delete(route('master-data.destroy', ['type' => 'current-actions', 'id' => $item->id]))
        ->assertRedirect(route('master-data.manage', 'current-actions'));

    expect(CurrentAction::query()->find($item->id))->toBeNull();
});

test('master data cannot be deleted when used in classification', function () {
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create(['code' => 'D999']);

    Classification::factory()->for($user)->create([
        'doctor_id' => $doctor->id,
    ]);

    $this->actingAs($user)
        ->delete(route('master-data.destroy', ['type' => 'doctors', 'id' => $doctor->id]))
        ->assertRedirect(route('master-data.manage', 'doctors'));

    expect(Doctor::query()->find($doctor->id))->not->toBeNull();
});
