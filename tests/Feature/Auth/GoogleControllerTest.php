<?php

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

test('google login redirects to the provider', function () {
    Socialite::shouldReceive('driver')->once()->with('google')->andReturnSelf();
    Socialite::shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com'));

    $this->get(route('auth.google'))
        ->assertRedirect('https://accounts.google.com');
});

test('google callback creates and authenticates a user without a family', function () {
    Role::findOrCreate('family-member', 'web');
    $googleUser = (object) [
        'id' => 'google-123',
        'name' => 'Google User',
        'email' => 'google@example.com',
        'avatar' => 'https://example.com/avatar.jpg',
    ];
    Socialite::shouldReceive('driver')->once()->with('google')->andReturnSelf();
    Socialite::shouldReceive('user')->once()->andReturn($googleUser);

    $this->get('/auth/google/callback')
        ->assertRedirect('/family/setup');

    $user = User::where('google_id', 'google-123')->firstOrFail();
    expect(Auth::id())->toBe($user->id);
    expect($user->name)->toBe('Google User');
    expect($user->hasRole('family-member'))->toBeTrue();
});

test('google callback updates an existing user and redirects family members to dashboard', function () {
    $family = Family::factory()->create();
    $user = User::factory()->create([
        'family_id' => $family->id,
        'email' => 'existing@example.com',
    ]);
    $googleUser = (object) [
        'id' => 'google-existing',
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => 'https://example.com/new-avatar.jpg',
    ];
    Socialite::shouldReceive('driver')->once()->with('google')->andReturnSelf();
    Socialite::shouldReceive('user')->once()->andReturn($googleUser);

    $this->get('/auth/google/callback')
        ->assertRedirect('/dashboard');

    expect($user->refresh()->google_id)->toBe('google-existing');
    expect($user->avatar)->toBe('https://example.com/new-avatar.jpg');
    expect(Auth::id())->toBe($user->id);
});

test('google callback redirects to login when the provider fails', function () {
    Socialite::shouldReceive('driver')->once()->with('google')->andReturnSelf();
    Socialite::shouldReceive('user')->once()->andThrow(new RuntimeException('Provider unavailable'));

    $this->get('/auth/google/callback')
        ->assertRedirect(route('login'))
        ->assertSessionHas('error');
    $this->assertGuest();
});
