<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonImmutable;

class DashboardService
{
    /**
     * @return array<string, mixed>|null
     */
    public function getDashboardData(User $user): ?array
    {
        $user->load('family.categories', 'family.users');

        $family = $user->family;
        if (! $family) {
            return null;
        }

        $startOfMonth = CarbonImmutable::now()->startOfMonth();
        $endOfMonth = CarbonImmutable::now()->endOfMonth();

        $monthlyIncome = $family->transactions()
            ->where('type', 'income')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthlyExpenses = $family->transactions()
            ->where('type', 'expense')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $recentTransactions = $family->transactions()
            ->with(['user:id,name', 'category:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        return [
            'family' => [
                'name' => $family->name,
                'invite_code' => $family->invite_code,
                'total_balance' => $family->total_balance,
            ],
            'is_family_head' => $user->hasRole('family-head'),
            'current_user_id' => $user->id,
            'members' => $family->users->map(fn($member) => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
            ])->values(),
            'categories' => $family->categories->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
            ])->values(),
            'monthly_income' => (float) $monthlyIncome,
            'monthly_expenses' => (float) $monthlyExpenses,
            'recent_transactions' => $recentTransactions,
        ];
    }
}
