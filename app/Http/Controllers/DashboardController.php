<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Eager load the family to avoid N+1 query issues
        $user->load('family');
        $family = $user->family;

        // Security check: if no family is linked, force setup
        if (!$family) {
            return redirect('/family/setup');
        }

        return Inertia::render('Family/Dashboard', [
            'family' => [
                'name' => $family->name,
                'invite_code' => $family->invite_code,
                'total_balance' => $family->total_balance,
            ],
            // We will fetch real transactions later
            'recent_transactions' => []
        ]);
    }
}
