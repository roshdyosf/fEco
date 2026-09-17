<?php

use App\Models\Family;
use App\Models\User;
use Spatie\Permission\Models\Role;

function familyHeadRole(): Role
{
    return Role::findOrCreate('family-head', 'web');
}

function familyMemberRole(): Role
{
    return Role::findOrCreate('family-member', 'web');
}

test('guests are redirected from family setup', function () {
    $this->get(route('family.setup'))
        ->assertRedirect(route('login'));
});

test('a user without a family can view the setup page', function () {
    $user = User::factory()->create(['family_id' => null]);

    $this->actingAs($user)
        ->get(route('family.setup'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Family/Setup'));
});

test('a user with a family is redirected from setup to the dashboard', function () {
    $family = Family::factory()->create();
    $user = User::factory()->create(['family_id' => $family->id]);

    $this->actingAs($user)
        ->get(route('family.setup'))
        ->assertRedirect(route('dashboard'));
});

test('a user can create a family and becomes its head', function () {
    familyHeadRole();
    $user = User::factory()->create(['family_id' => null]);

    $response = $this->actingAs($user)->post(route('family.create'), [
        'name' => 'Smith Family',
    ]);

    $response->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');
    $user->refresh();

    $this->assertDatabaseHas('families', [
        'id' => $user->family_id,
        'name' => 'Smith Family',
    ]);
    expect($user->hasRole(familyHeadRole()))->toBeTrue();
});

test('family creation requires a name', function () {
    $user = User::factory()->create(['family_id' => null]);

    $this->actingAs($user)
        ->from(route('family.setup'))
        ->post(route('family.create'))
        ->assertSessionHasErrors('name');
});

test('a user can join a family with a valid invite code', function () {
    familyMemberRole();
    $family = Family::factory()->create();
    $user = User::factory()->create(['family_id' => null]);

    $response = $this->actingAs($user)->post(route('family.join'), [
        'invite_code' => $family->invite_code,
    ]);

    $response->assertRedirect(route('dashboard'));
    expect($user->refresh()->family_id)->toBe($family->id);
    expect($user->hasRole(familyMemberRole()))->toBeTrue();
});

test('joining with an unknown invite code returns a validation error', function () {
    $user = User::factory()->create(['family_id' => null]);

    $this->actingAs($user)
        ->from(route('family.setup'))
        ->post(route('family.join'), ['invite_code' => 'UNKNOWN1'])
        ->assertSessionHasErrors('invite_code');
    expect($user->refresh()->family_id)->toBeNull();
});

test('a family member can leave their family', function () {
    $family = Family::factory()->create();
    $user = User::factory()->create(['family_id' => $family->id]);
    $user->assignRole(familyMemberRole());

    $this->actingAs($user)
        ->post(route('family.leave'))
        ->assertRedirect(route('family.setup'))
        ->assertSessionHas('success');

    expect($user->refresh()->family_id)->toBeNull();
    expect($user->hasRole('family-member'))->toBeFalse();
});

test('a family head can delete the family', function () {
    $family = Family::factory()->create();
    $user = User::factory()->create(['family_id' => $family->id]);
    $user->assignRole(familyHeadRole());

    $this->actingAs($user)
        ->delete(route('family.destroy'))
        ->assertRedirect(route('family.setup'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('families', ['id' => $family->id]);
    expect($user->refresh()->family_id)->toBeNull();
});

test('a family head can remove another member', function () {
    $family = Family::factory()->create();
    $head = User::factory()->create(['family_id' => $family->id]);
    $member = User::factory()->create(['family_id' => $family->id]);
    $head->assignRole(familyHeadRole());
    $member->assignRole(familyMemberRole());

    $this->actingAs($head)
        ->delete(route('family.member.destroy', $member))
        ->assertSessionHas('success');

    expect($member->refresh()->family_id)->toBeNull();
    expect($member->hasRole('family-member'))->toBeFalse();
});

test('a family member cannot leave without belonging to a family', function () {
    $user = User::factory()->create(['family_id' => null]);

    $this->actingAs($user)
        ->post(route('family.leave'))
        ->assertForbidden();
});
