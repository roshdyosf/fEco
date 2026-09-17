<?php

namespace App\Policies;

use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        // If user has Spatie permission to view all transactions
        return $user->can('view-all-transactions');
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->can('create-transaction');
    }
}
