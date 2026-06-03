<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'create-questions',
            'edit-questions',
            'delete-questions',
            'view-questions',
            'manage-assessments',
            'grade-essays',
            'view-live-leaderboard',
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
            Permission::findOrCreate($permission, 'web');
        }

        // Create Roles and Assign Permissions
        $superAdmin = Role::findOrCreate('Super Admin', 'api');
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'api')->get());

        Role::findOrCreate('HR Manager', 'api')->givePermissionTo([
            'manage-assessments',
            'view-live-leaderboard',
            'manage-users',
        ]);

        Role::findOrCreate('Assessor', 'api')->givePermissionTo([
            'create-questions',
            'edit-questions',
            'view-questions',
            'grade-essays',
            'view-live-leaderboard',
        ]);

        Role::findOrCreate('Peserta', 'api');
    }
}
