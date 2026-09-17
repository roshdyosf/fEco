<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Family;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createTransaction(array $data, User $user): Transaction
    {
        return DB::transaction(function () use ($data, $user) {
            Category::query()
                ->whereKey($data['category_id'])
                ->where('family_id', $user->family_id)
                ->where('type', $data['type'])
                ->lockForUpdate()
                ->firstOrFail();

            $transaction = Transaction::create([
                'family_id' => $user->family_id,
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'amount' => $data['amount'],
                'type' => $data['type'],
                'description' => $data['description'] ?? null,
            ]);

            $family = Family::lockForUpdate()->findOrFail($user->family_id);

            if ($data['type'] === 'income') {
                $family->total_balance += $data['amount'];
            } else {
                $family->total_balance -= $data['amount'];
            }

            $family->save();

            return $transaction;
        });
    }
}
