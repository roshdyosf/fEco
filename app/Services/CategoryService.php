<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * @return Collection<int, Category>
     */
    public function getFamilyCategories(User $user): Collection
    {
        return Category::query()
            ->where('family_id', $user->family_id)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);
    }

    /**
     * @return array{top_expense_categories: list<array{id: int, name: string, total: float}>, total_income: float}
     */
    public function getFamilyStatistics(User $user): array
    {
        // Fetch top expense categories efficiently

        $topExpenseCategories = Category::query()
            ->where('family_id', $user->family_id)
            ->where('type', 'expense')
            ->has('transactions') // Ensures only categories with transactions are selected
            ->withSum([
                'transactions as total' => fn ($query) => $query
                    ->where('family_id', $user->family_id)
                    ->where('type', 'expense'),
            ], 'amount')
            ->orderByDesc('total')
            ->limit(3)
            ->get(['id', 'name'])
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'total' => (float) ($category->total ?? 0),
            ])
            ->all();

        $totalIncome = Transaction::query()
            ->where('family_id', $user->family_id)
            ->where('type', 'income')
            ->sum('amount');

        return [
            'top_expense_categories' => $topExpenseCategories,
            'total_income' => (float) $totalIncome,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createCategory(array $data, User $user): Category
    {
        return Category::create([
            'family_id' => $user->family_id,
            'name' => $data['name'],
            'type' => $data['type'],
        ]);
    }

    public function deleteCategory(Category $category): void
    {
        if ($category->transactions()->exists()) {
            throw new DomainException('Categories with transactions cannot be deleted.');
        }

        $category->delete();
    }
}
