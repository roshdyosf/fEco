<?php

use App\Models\Category;
use App\Models\Family;
use App\Models\Transaction;
use App\Models\User;

function categoryUser(?Family $family = null): User
{
    return User::create([
        'family_id' => $family?->id,
        'name' => 'Family Member',
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
    ]);
}

function categoryFamily(string $name = 'Test Family'): Family
{
    return Family::create([
        'name' => $name,
        'invite_code' => fake()->unique()->bothify('????####'),
        'total_balance' => 0,
    ]);
}

test('a family member can create a category for their family', function () {
    $family = categoryFamily();
    $user = categoryUser($family);

    $response = $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Groceries',
        'type' => 'expense',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('categories', [
        'family_id' => $family->id,
        'name' => 'Groceries',
        'type' => 'expense',
    ]);
});

test('guests cannot create a category', function () {
    $this->post(route('categories.store'), [
        'name' => 'Groceries',
        'type' => 'expense',
    ])->assertRedirect(route('login'));
});

test('category creation requires a name and type', function () {
    $family = categoryFamily();
    $user = categoryUser($family);

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('categories.store'))
        ->assertSessionHasErrors(['name', 'type']);

    $this->assertDatabaseMissing('categories', ['family_id' => $family->id]);
});

test('a category name must be unique within a family', function () {
    $family = categoryFamily();
    $user = categoryUser($family);
    Category::create([
        'family_id' => $family->id,
        'name' => 'Groceries',
        'type' => 'expense',
    ]);

    $response = $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Groceries',
        'type' => 'income',
    ]);

    $response->assertSessionHasErrors('name');
});

test('a family member can delete an unused category in their family', function () {
    $family = categoryFamily();
    $user = categoryUser($family);
    $category = Category::create([
        'family_id' => $family->id,
        'name' => 'Subscriptions',
        'type' => 'expense',
    ]);

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('a family member cannot delete another family category', function () {
    $userFamily = categoryFamily('User Family');
    $otherFamily = categoryFamily('Other Family');
    $user = categoryUser($userFamily);
    $category = Category::create([
        'family_id' => $otherFamily->id,
        'name' => 'Private',
        'type' => 'expense',
    ]);

    $this->actingAs($user)
        ->delete(route('categories.destroy', $category))
        ->assertForbidden();
});

test('a category with transactions cannot be deleted', function () {
    $family = categoryFamily();
    $user = categoryUser($family);
    $category = Category::create([
        'family_id' => $family->id,
        'name' => 'Rent',
        'type' => 'expense',
    ]);
    Transaction::create([
        'family_id' => $family->id,
        'user_id' => $user->id,
        'category_id' => $category->id,
        'amount' => 100,
        'type' => 'expense',
        'description' => 'Monthly rent',
    ]);

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
