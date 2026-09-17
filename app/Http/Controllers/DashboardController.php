<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Load family and its categories
        $user->load('family.categories', 'family.users');
        $family = $user->family;

        if (! $family) {
            return redirect('/family/setup');
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Calculate monthly income using created_at
        $monthlyIncome = $family->transactions()
            ->where('type', 'income')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        // Calculate monthly expenses using created_at
        $monthlyExpenses = $family->transactions()
            ->where('type', 'expense')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        // Fetch recent transactions with relations
        $recentTransactions = $family->transactions()
            ->with(['user:id,name', 'category:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'family' => [
                'name' => $family->name,
                'invite_code' => $family->invite_code,
                'total_balance' => $family->total_balance,
            ],
            'is_family_head' => $user->hasRole('family-head'),
            'current_user_id' => $user->id,
            'members' => $family->users->map(fn ($member) => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
            ])->values(),
            'categories' => $family->categories->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
            ])->values(),
            'monthly_income' => $monthlyIncome,
            'monthly_expenses' => $monthlyExpenses,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}
