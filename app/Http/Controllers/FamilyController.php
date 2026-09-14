<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FamilyController extends Controller
{
    // Show family setup page
    public function showSetup()
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->family_id) {
            return redirect()->route('dashboard');
        }
        return Inertia::render('Family/Setup');
    }

    // Family creation logic
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Create the family
        $family = Family::create([
            'name' => $request->name,
            'invite_code' => Str::upper(Str::random(8)),
            'total_balance' => 0.00,
        ]);

        // Link the user to the family and save using direct property assignment
        $user->family_id = $family->id;
        $user->save();

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('family-head');
        }

        return redirect()->route('dashboard')->with('success', 'Family created successfully! Your invite code is: ' . $family->invite_code);
    }

    // Join family using invite code
    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:families,invite_code',
        ]);

        /** @var User $user */
        $user = $request->user();

        $family = Family::where('invite_code', $request->invite_code)->first();

        // Link the user to the family and save
        $user->family_id = $family->id;
        $user->save();

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('family-member');
        }

        return redirect('/dashboard')->with('success', 'You have successfully joined the family: ' . $family->name);
    }
}
