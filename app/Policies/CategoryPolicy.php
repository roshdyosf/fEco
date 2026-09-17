<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function create(User $user): bool
    {
        return $user->family_id !== null;
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->family_id !== null
            && $user->family_id === $category->family_id;
    }
}
