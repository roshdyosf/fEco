<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use DomainException;

class CategoryService
{
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
