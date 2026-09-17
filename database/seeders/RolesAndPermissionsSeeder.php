<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-family-members',
            'manage-categories',
            'view-all-transactions',
            'create-transaction',
            'edit-own-transaction',
            'delete-own-transaction',
            'manage-budgets',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Family Head Role (gets all permissions)
        $familyHeadRole = Role::firstOrCreate(['name' => 'family-head']);
        $familyHeadRole->givePermissionTo(Permission::all());

        // Family Member Role (gets specific permissions)
        $familyMemberRole = Role::firstOrCreate(['name' => 'family-member']);
        $familyMemberRole->givePermissionTo([
            'create-transaction',
            'edit-own-transaction',
            'delete-own-transaction',
        ]);
    }
}
