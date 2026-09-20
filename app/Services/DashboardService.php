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

        $categoriesKeyed = $family->categories->keyBy('id');

        $startOfMonth = CarbonImmutable::now()->startOfMonth();
        $endOfMonth = CarbonImmutable::now()->endOfMonth();

        $transactions = $family->transactions()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->with(['user:id,name'])
            ->latest()
            ->get();

        $transactions->transform(function ($transaction) use ($categoriesKeyed) {
            if ($transaction->category_id && $categoriesKeyed->has($transaction->category_id)) {
                $transaction->setRelation('category', $categoriesKeyed->get($transaction->category_id));
            }

            return $transaction;
        });

        $monthlyIncome = $transactions
            ->where('type', 'income')
            ->sum('amount');

        $monthlyExpenses = $transactions
            ->where('type', 'expense')
            ->sum('amount');

        $recentTransactions = $transactions->take(10)->values();

        return [
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
            'monthly_income' => (float) $monthlyIncome,
            'monthly_expenses' => (float) $monthlyExpenses,
            'recent_transactions' => $recentTransactions,
        ];
    }
}
