<?php

use App\Enums\ClassificationStatus;
use App\Models\Classification;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('dashboard shows classification summary for authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Classification::factory()->count(2)->for($user)->create([
        'status' => ClassificationStatus::Classified,
        'prediction_result' => 'PRO IVF',
        'created_at' => now(),
    ]);

    Classification::factory()->for($user)->noRule()->create([
        'created_at' => now()->subDay(),
    ]);

    Classification::factory()->for($other)->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('totalClassifications', 3)
            ->where('todayClassifications', 2)
            ->where('classifiedCount', 2)
            ->where('noRuleCount', 1)
            ->has('recentClassifications', 3)
            ->has('resultDistribution', 1)
            ->where('resultDistribution.0.label', 'PRO IVF')
            ->where('resultDistribution.0.total', 2)
        );
});
