<?php

namespace App\Services;

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FamilyService
{
    public function generateRandom(): string
    {
        $num = Str::upper(Str::random(8));
        while (Family::where('invite_code', $num)->exists()) {
            $num = Str::upper(Str::random(8));
        }

        return $num;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createFamily(array $data, User $user): Family
    {
        return DB::transaction(function () use ($data, $user): Family {
            $family = Family::create([
                'name' => $data['name'],
                'invite_code' => $this->generateRandom(),
                'total_balance' => 0,
            ]);

            $user->update(['family_id' => $family->id]);
            $user->assignRole('family-head');

            return $family;
        });
    }

    public function joinFamily(string $inviteCode, User $user): bool
    {
        return DB::transaction(function () use ($inviteCode, $user): bool {
            $family = Family::where('invite_code', $inviteCode)->first();

            if (! $family) {
                return false;
            }

            $user->update(['family_id' => $family->id]);
            $user->assignRole('family-member');

            return true;
        });
    }

    public function regenerateCode(Family $family): void
    {
        $family->update([
            'invite_code' => $this->generateRandom(),
        ]);
    }

    public function removeMember(User $member, User $head): bool
    {
        if ($member->family_id !== $head->family_id || $member->id === $head->id) {
            return false;
        }

        DB::transaction(function () use ($member, $head): void {
            $member->update(['family_id' => null]);
            $member->removeRole('family-member');
            $head->family->update(['invite_code' => $this->generateRandom()]);
        });

        return true;
    }

    public function leaveFamily(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $family = $user->family;
            $user->update(['family_id' => null]);
            $user->removeRole('family-member');
            if ($family) {
                $family->update(['invite_code' => $this->generateRandom()]);
            }
        });
    }

    public function deleteFamily(User $head): void
    {
        DB::transaction(function () use ($head): void {
            $family = Family::query()
                ->whereKey($head->family_id)
                ->lockForUpdate()
                ->firstOrFail();

            $members = $family->users()->get();

            foreach ($members as $member) {
                $member->removeRole('family-head');
                $member->removeRole('family-member');
                $member->update(['family_id' => null]);
            }

            $family->delete();
        });
    }
}
