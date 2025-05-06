<?php
namespace Database\Seeders;

use App\Domains\Authorization\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'view_users',
            'view_orders',
            'create_users',
            'edit_users',
            'delete_users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and assign permissions
        $roles = [
            RoleEnum::ADMIN->value => [
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
            ],
            RoleEnum::MODERATOR->value => [
                'view_orders',
            ],
            RoleEnum::USER->value => [
                'view_orders',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            // Создаем или получаем роль
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Назначаем разрешения роли
            $role->syncPermissions($rolePermissions);
        }
    }
}
