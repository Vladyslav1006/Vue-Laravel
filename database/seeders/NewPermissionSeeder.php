<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class NewPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {

        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            ['name' => 'user_edit', 'perm_label' => null],
            ['name' => 'user_create', 'perm_label' => null],
            ['name' => 'user_export', 'perm_label' => null],
            ['name' => 'user_delete', 'perm_label' => null],
            ['name' => 'user_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'signinlog_export', 'perm_label' => null],
            ['name' => 'signinlog_delete', 'perm_label' => null],
            ['name' => 'signinlog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'jobrequest_entertoedit', 'perm_label' => 'Edit with ENTER key'],
            ['name' => 'jobrequest_DisViewAllRow', 'perm_label' => 'Disable View All Rows'],
            ['name' => 'jobrequest_edit', 'perm_label' => null],
            ['name' => 'jobrequest_DisCheckboxes', 'perm_label' => 'Disable Checkboxes'],
            ['name' => 'jobrequest_duplicate', 'perm_label' => null],
            ['name' => 'jobrequest_limCheckSelect', 'perm_label' => 'Limit to X Checkboxes Select'],
            ['name' => 'jobrequest_export', 'perm_label' => null],
            ['name' => 'jobrequest_listAll', 'perm_label' => 'View All CRDR1 Assigned'],
            ['name' => 'jobrequest_CRDRchange', 'perm_label' => 'Can Change CRDR1'],
            ['name' => 'jobrequest_delete', 'perm_label' => null],
            ['name' => 'jobrequest_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'role_edit', 'perm_label' => null],
            ['name' => 'role_create', 'perm_label' => null],
            ['name' => 'role_export', 'perm_label' => null],
            ['name' => 'role_delete', 'perm_label' => null],
            ['name' => 'role_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'activitylog_export', 'perm_label' => null],
            ['name' => 'activitylog_delete', 'perm_label' => null],
            ['name' => 'activitylog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'bbapplicant_entertoedit', 'perm_label' => 'Edit with ENTER key'],
            ['name' =>  'bbapplicant_DisViewAllRow', 'perm_label' => 'Disable View All Rows'],
            ['name' => 'bbapplicant_edit', 'perm_label' => null],
            ['name' => 'bbapplicant_DisCheckboxes', 'perm_label' => 'Disable Checkboxes'],
            ['name' => 'bbapplicant_duplicate', 'perm_label' => 'Duplicate'],
            ['name' => 'bbapplicant_limCheckSelect', 'perm_label' => 'Limit to X Checkboxes Select'],
            ['name' => 'bbapplicant_export', 'perm_label' => null],
            ['name' => 'bbapplicant_delete', 'perm_label' => null],
            ['name' => 'bbapplicant_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'jobsearch_entertoedit', 'perm_label' => 'Edit with ENTER key'],
            ['name' => 'jobsearch_DisViewAllRow', 'perm_label' => 'Disable View All Rows'],
            ['name' => 'jobsearch_edit', 'perm_label' => null],
            ['name' => 'jobsearch_DisCheckboxes', 'perm_label' => 'Disable Checkboxes'],
            ['name' => 'jobsearch_duplicate', 'perm_label' => null],
            ['name' => 'jobsearch_limCheckSelect', 'perm_label' => 'Limit to X Checkboxes Select'],
            ['name' => 'jobsearch_export', 'perm_label' => null],
            ['name' => 'jobsearch_listAll', 'perm_label' => 'View All CRDR1 Assigned'],
            ['name' => 'jobsearch_CRDRchange', 'perm_label' => 'Can Change CRDR1'],
            ['name' => 'jobsearch_delete', 'perm_label' => null],
            ['name' => 'jobsearch_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'permission_create', 'perm_label' => null],
            ['name' => 'permission_edit', 'perm_label' => null],
            ['name' => 'permission_delete', 'perm_label' => null],
            ['name' => 'permission_list', 'perm_label' => null],
            //    ['name'=>'publicholiday_list','perm_label'=>null],
            //    ['name'=> 'publicholiday_create','perm_label'=>null],  
            //    ['name'=>'publicholiday_edit','perm_label'=>null],
            //    ['name'=> 'publicholiday_delete','perm_label'=>null],  
            //    ['name'=>'publicholiday_export','perm_label'=>null]
        ];





        foreach ($permissions as $permission) {
            // \Log::info($permission['name']);
            Permission::create([
                'name' => $permission['name'],
                'perm_label' => $permission['perm_label']
            ]);
        }

        $super_admin = Role::where(['name' => 'super-admin'])->first();
        foreach ($permissions as $permission) {
            $super_admin->givePermissionTo($permission['name']);
        }

        $admin_permission = [
            ['name' => 'user_edit', 'perm_label' => null],
            ['name' => 'user_create', 'perm_label' => null],
            ['name' => 'user_export', 'perm_label' => null],
            ['name' => 'user_delete', 'perm_label' => null],
            ['name' => 'user_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'signinlog_export', 'perm_label' => null],
            ['name' => 'signinlog_delete', 'perm_label' => null],
            ['name' => 'signinlog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'role_edit', 'perm_label' => null],
            ['name' => 'role_create', 'perm_label' => null],
            ['name' => 'role_export', 'perm_label' => null],
            ['name' => 'role_delete', 'perm_label' => null],
            ['name' => 'role_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'activitylog_export', 'perm_label' => null],
            ['name' => 'activitylog_delete', 'perm_label' => null],
            ['name' => 'activitylog_list', 'perm_label' => 'Activate Mod'],

        ];
        $admin = Role::where(['name' => 'admin'])->first();
        if (!is_null($admin)) {
            foreach ($admin_permission as $permission) {
                $admin->givePermissionTo($permission['name']);
            }
        }


        $coordinator_permission = [
            ['name' => 'user_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'signinlog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'activitylog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'jobrequest_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'jobrequest_listAll', 'perm_label' => 'View ALL CRDR1 Assigned'],
            ['name' => 'jobrequest_delete', 'perm_label' => null],
            ['name' => 'jobrequest_export', 'perm_label' => null],
            ['name' => 'jobrequest_CRDRchange', 'perm_label' => 'Can Change CRDR1'],
            ['name' => 'jobrequest_edit', 'perm_label' => null],
            ['name' => 'jobrequest_duplicate', 'perm_label' => null],
            ['name' => 'jobrequest_limCheckSelect', 'perm_label' => 'Limit to X Checkboxes Select'],
            ['name' =>  'bbapplicant_DisViewAllRow', 'perm_label' => 'Disable View All Rows'],
            ['name' => 'bbapplicant_edit', 'perm_label' => null],
            ['name' => 'bbapplicant_duplicate', 'perm_label' => 'Duplicate'],
            ['name' => 'bbapplicant_limCheckSelect', 'perm_label' => 'Limit to X Checkboxes Select'],
            ['name' => 'bbapplicant_export', 'perm_label' => null],
            ['name' => 'bbapplicant_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'jobsearch_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'jobsearch_edit', 'perm_label' => null],
            ['name' => 'jobsearch_export', 'perm_label' => null],
            ['name' => 'jobsearch_listAll', 'perm_label' => 'View ALL CRDR1 Assigned'],
            ['name' => 'jobsearch_CRDRchange', 'perm_label' => 'Can Change CRDR1'],
            ['name' => 'jobsearch_delete', 'perm_label' => null],

        ];
        $coordinator = Role::where(['name' => 'Coordinator'])->first();
        if (!is_null($coordinator)) {
            foreach ($coordinator_permission as $permission) {
                $coordinator->givePermissionTo($permission['name']);
            }
        }


        $teser_permission = [
            ['name' => 'role_edit', 'perm_label' => null],
            ['name' => 'role_create', 'perm_label' => null],
            ['name' => 'role_delete', 'perm_label' => null],
            ['name' => 'role_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'user_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'user_create', 'perm_label' => null],
            ['name' => 'user_delete', 'perm_label' => null],
            ['name' => 'signinlog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'signinlog_delete', 'perm_label' => null],
            ['name' => 'signinlog_export', 'perm_label' => null],
            ['name' => 'activitylog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'activitylog_export', 'perm_label' => null],
            ['name' => 'activitylog_delete', 'perm_label' => null],
        ];
        $teser = Role::where(['name' => 'TESER'])->first();
        if (!is_null($teser)) {
            foreach ($teser_permission as $permission) {
                $teser->givePermissionTo($permission['name']);
            }
        }

        $limei_permissions = [
            ['name' => 'role_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'user_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'signinlog_export', 'perm_label' => null],
            ['name' => 'signinlog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'activitylog_export', 'perm_label' => null],
            ['name' => 'activitylog_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'user_export', 'perm_label' => null],
            ['name' => 'role_export', 'perm_label' => null],
            ['name' => 'jobrequest_entertoedit', 'perm_label' => 'Edit with ENTER key'],
            ['name' => 'jobrequest_edit', 'perm_label' => null],
            ['name' => 'jobrequest_duplicate', 'perm_label' => null],
            ['name' => 'jobrequest_export', 'perm_label' => null],
            ['name' => 'jobrequest_listAll', 'perm_label' => 'View All CRDR1 Assigned'],
            ['name' => 'jobrequest_list', 'perm_label' => 'Activate Mod'],
            ['name' => 'bbapplicant_entertoedit', 'perm_label' => 'Edit with ENTER key'],
            ['name' => 'bbapplicant_edit', 'perm_label' => null],
            ['name' => 'bbapplicant_duplicate', 'perm_label' => 'Duplicate'],
            ['name' => 'bbapplicant_export', 'perm_label' => null],
            ['name' => 'bbapplicant_list', 'perm_label' => 'Activate Mod'],
        ];
        $limei = Role::where(['name' => 'LIMEI'])->first();
        if (!is_null($limei)) {
            foreach ($limei_permissions as $permission) {
                $limei->givePermissionTo($permission['name']);
            }
        }
    }
}
