<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleController extends Controller
{
    public function redirectToGoogle(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            /** @var object{id: string, email: string, name: string, avatar: string} $googleUser */
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Update existing user attributes safely using save()
                $user->google_id = $googleUser->id;
                $user->avatar = $googleUser->avatar;
                $user->save();
            } else {
                // Create a new user if not found
                $user = User::create([
                    'family_id' => null,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => bcrypt(Str::random(16)),
                ]);
                $user->assignRole('family-member');
            }
            // login the user
            Auth::login($user);
            $request->session()->regenerate();

            // redirect based on whether the user has a family or not
            if (! $user->family_id) {
                return redirect('/family/setup');
            }

            return redirect('/dashboard');
        } catch (\Throwable $e) {
            Log::error('Google OAuth login failed.', ['exception' => $e]);

            return redirect('/login')->with(
                'error',
                __('Unable to sign in with Google. Please try again.'),
            );
        }
    }
}
