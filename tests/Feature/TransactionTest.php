<?php

use App\Models\Category;
use App\Models\Family;
use App\Models\Transaction;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function transactionUser(Family $family): User
{
    $user = User::factory()->create(['family_id' => $family->id]);
    Permission::findOrCreate('view-all-transactions', 'web');

    return $user;
}

test('guests cannot view transactions', function () {
    $this->get(route('transactions.index'))
        ->assertRedirect(route('login'));
});

test('a permitted family member can view paginated transactions', function () {
    $family = Family::factory()->create();
    $user = transactionUser($family);
    $user->givePermissionTo('view-all-transactions');
    $category = Category::factory()->create(['family_id' => $family->id]);
    $transaction = Transaction::factory()->create([
        'family_id' => $family->id,
        'user_id' => $user->id,
        'category_id' => $category->id,
        'description' => 'Rent payment',
    ]);

    $this->actingAs($user)
        ->get(route('transactions.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Transactions/Index')
                ->where('transactions.data.0.id', $transaction->id)
                ->where('transactions.data.0.description', 'Rent payment')
                ->missing('statistics')
        );
});

test('only a family head sees family transaction statistics', function () {
    $family = Family::factory()->create();
    $head = transactionUser($family);
    Role::findOrCreate('family-head', 'web');
    $head->assignRole('family-head');
    $head->givePermissionTo('view-all-transactions');
    $rent = Category::factory()->create([
        'family_id' => $family->id,
        'name' => 'Rent',
        'type' => 'expense',
    ]);
    $food = Category::factory()->create([
        'family_id' => $family->id,
        'name' => 'Food',
        'type' => 'expense',
    ]);
    $income = Category::factory()->create([
        'family_id' => $family->id,
        'type' => 'income',
    ]);
    Transaction::factory()->create([
        'family_id' => $family->id,
        'user_id' => $head->id,
        'category_id' => $rent->id,
        'amount' => 900,
        'type' => 'expense',
    ]);
    Transaction::factory()->create([
        'family_id' => $family->id,
        'user_id' => $head->id,
        'category_id' => $food->id,
        'amount' => 125,
        'type' => 'expense',
    ]);
    Transaction::factory()->create([
        'family_id' => $family->id,
        'user_id' => $head->id,
        'category_id' => $income->id,
        'amount' => 2500,
        'type' => 'income',
    ]);
    $otherFamily = Family::factory()->create();
    $otherCategory = Category::factory()->create([
        'family_id' => $otherFamily->id,
        'type' => 'expense',
    ]);
    Transaction::factory()->create([
        'family_id' => $otherFamily->id,
        'category_id' => $otherCategory->id,
        'amount' => 9999,
        'type' => 'expense',
    ]);

    $this->actingAs($head)
        ->get(route('transactions.index', ['category_id' => $food->id]))
        ->assertInertia(
            fn ($page) => $page
                ->where('transactions.total', 1)
                ->where('transactions.data.0.category.id', $food->id)
                ->where('statistics.total_income', 2500)
                ->where('statistics.top_expense_categories.0.name', 'Rent')
                ->where('statistics.top_expense_categories.0.total', 900)
                ->where('statistics.top_expense_categories.1.name', 'Food')
                ->where('statistics.top_expense_categories.1.total', 125)
        );
});

test('a user without transaction permission cannot view all transactions', function () {
    $family = Family::factory()->create();
    $user = User::factory()->create(['family_id' => $family->id]);

    $this->actingAs($user)
        ->get(route('transactions.index'))
        ->assertForbidden();
});

test('a family member can create an income transaction and update the balance', function () {
    $family = Family::factory()->create(['total_balance' => 0]);
    $user = User::factory()->create(['family_id' => $family->id]);
    Permission::findOrCreate('create-transaction', 'web');
    $user->givePermissionTo('create-transaction');
    $category = Category::factory()->create([
        'family_id' => $family->id,
        'type' => 'income',
    ]);

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('transactions.store'), [
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 125.50,
            'description' => 'Salary',
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('transactions', [
        'family_id' => $family->id,
        'user_id' => $user->id,
        'category_id' => $category->id,
        'amount' => 125.50,
        'type' => 'income',
    ]);
    expect((float) $family->refresh()->total_balance)->toBe(125.5);
});

test('transaction creation rejects an empty payload', function () {
    $family = Family::factory()->create();
    $user = User::factory()->create(['family_id' => $family->id]);
    Permission::findOrCreate('create-transaction', 'web');
    $user->givePermissionTo('create-transaction');

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('transactions.store'))
        ->assertSessionHasErrors(['type', 'category_id', 'amount']);

    $this->assertDatabaseCount('transactions', 0);
});

test('a transaction category must belong to the users family', function () {
    $family = Family::factory()->create();
    $otherFamily = Family::factory()->create();
    $user = User::factory()->create(['family_id' => $family->id]);
    Permission::findOrCreate('create-transaction', 'web');
    $user->givePermissionTo('create-transaction');
    $category = Category::factory()->create(['family_id' => $otherFamily->id]);

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('transactions.store'), [
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 10,
        ])
        ->assertSessionHasErrors('category_id');

    $this->assertDatabaseCount('transactions', 0);
});

test('a transaction type must match its category type', function () {
    $family = Family::factory()->create();
    $user = User::factory()->create(['family_id' => $family->id]);
    $category = Category::factory()->create([
        'family_id' => $family->id,
        'type' => 'income',
    ]);
    Permission::findOrCreate('create-transaction', 'web');
    $user->givePermissionTo('create-transaction');

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('transactions.store'), [
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 10,
        ])
        ->assertSessionHasErrors('category_id');

    $this->assertDatabaseCount('transactions', 0);
});
