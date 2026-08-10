<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected from model information page', function () {
    $this->get(route('model-information'))->assertRedirect(route('login'));
});

test('authenticated users can view model information and decision rules', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('model-information'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('model/Index')
            ->where('model.algorithm', 'Decision Tree C4.5')
            ->where('model.training_count', 478)
            ->where('model.testing_count', 119)
            ->where('model.accuracy', 50.42)
            ->has('rules', 78)
            ->where('rules.0.code', 'R1')
            ->where('rules.77.code', 'R78')
        );
});
