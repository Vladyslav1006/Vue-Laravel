<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use Illuminate\Support\Facades\Hash;

class BasicAdminPermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
        $permissions = [
            'permission_list',
            'permission_create',
            'permission_edit',
            'permission_delete',
            'role_list',
            'role_create',
            'role_edit',
            'role_delete',
            'role_export',
            'user_list',
            'user_create',
            'user_edit',
            'user_delete',
            'user_export',
            'signinlog_list',
            'signinlog_delete',
            'signinlog_export',
            'activitylog_list',
            'activitylog_delete',
            'activitylog_export',
            'jobrequest_list',
            'jobrequest_listAll',
            'jobrequest_edit',
            'jobrequest_delete',
            'jobrequest_duplicate',
            'jobrequest_export',
            'jobrequest_CRDRchange',
            'publicholiday_list',
            'publicholiday_create',
            'publicholiday_edit',
            'publicholiday_delete',
            'publicholiday_export',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'supervisor']);
        //$role1->givePermissionTo('page_list');
        $role2 = Role::create(['name' => 'admin']);
        foreach ($permissions as $permission) {
            $role2->givePermissionTo($permission);
        }
        $role3 = Role::create(['name' => 'super-admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider
        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@superadmin.com',
            'password' => 'superadmin',
        ]);
        $user->assignRole($role3);
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $user->assignRole($role2);
        $user = \App\Models\User::factory()->create([
            'name' => 'supervisor User',
            'email' => 'supervisor@supervisor.com',
            'password' => 'supervisor',
        ]);
        $user->assignRole($role1);
    }
}
