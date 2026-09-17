<?php

use App\Models\Category;
use App\Models\Family;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $family = Family::factory()->create(['total_balance' => 250]);
    $user = User::factory()->create(['family_id' => $family->id]);
    $category = Category::factory()->create(['family_id' => $family->id]);
    $transaction = Transaction::factory()->create([
        'family_id' => $family->id,
        'user_id' => $user->id,
        'category_id' => $category->id,
        'amount' => 50,
        'type' => 'expense',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('family.name', $family->name)
                ->where('family.total_balance', 250)
                ->where('current_user_id', $user->id)
                ->where('monthly_expenses', 50)
                ->has('members', 1)
                ->has('categories', 1)
                ->where('recent_transactions.0.id', $transaction->id),
        );
});

test('authenticated users without a family are redirected to setup', function () {
    $user = User::factory()->create(['family_id' => null]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('family.setup'));
});
