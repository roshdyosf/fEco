<?php

namespace App\Policies;

use App\Models\Family;
use App\Models\User;

class FamilyPolicy
{
    public function delete(User $user, Family $family): bool
    {
        return $user->family_id === $family->id
            && $user->hasRole('family-head');
    }

    public function removeMember(User $user, Family $family, User $member): bool
    {
        return $user->family_id === $family->id
            && $member->family_id === $family->id
            && $member->isNot($user)
            && $user->hasRole('family-head');
    }
}
