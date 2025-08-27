<?php

namespace Database\Seeders\Roles;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        // premissions
        $permissions = [
            'bulk_action_user',
            'bulk_action_client',
            'all_users',
            'create_user',
            'edit_user',
            'update_user',
            'destroy_user',
            'change_user_status',

            'all_roles',
            'create_role',
            'edit_role',
            'update_role',
            'destroy_role',

            'all_clients',
            'create_client',
            'edit_client',
            'update_client',
            'destroy_client',


            'all_client_addresses',
            'create_client_address',
            'edit_client_address',
            'update_client_address',
            'destroy_client_address',

            'all_client_emails',
            'create_client_email',
            'edit_client_email',
            'update_client_email',
            'destroy_client_email',

            'all_client_phones',
            'create_client_phone',
            'edit_client_phone',
            'update_client_phone',
            'destroy_client_phone',

            // 'all_parameters',
            // 'create_parameter',
            // 'edit_parameter',
            // 'update_parameter',
            // 'destroy_parameter',

        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], [
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        // roles

        $admin = Role::create(['name' => 'admin']);
        $adminPermissions = [
            'all_users',
            'edit_user',
            'change_user_status',

            'all_clients',
            'edit_client',

            'all_client_addresses',
            'edit_client_address',

            'all_client_emails',
            'edit_client_email',

            'all_client_phones',
            'edit_client_phone',
        ];
        $admin->givePermissionTo($adminPermissions);
        $superAdmin = Role::create(['name' => 'super admin']);
        $superAdmin->givePermissionTo(Permission::get());

    }
}
