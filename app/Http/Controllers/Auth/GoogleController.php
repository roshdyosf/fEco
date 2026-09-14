<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Update the user's Google ID and avatar if they exist but don't have a Google ID
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                }
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
            }
            //login the user
            Auth::login($user);

            // redirect based on whether the user has a family or not
            if (!$user->family_id) {
                return redirect('/family/setup');
            }

            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with Google: ' . $e->getMessage());
        }
    }

}
