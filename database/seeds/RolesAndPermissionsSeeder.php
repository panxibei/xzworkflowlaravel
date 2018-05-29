<?php

use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

		// 重置角色和权限的缓存
        app()['cache']->forget('spatie.permission.cache');

		// 创建权限
		Permission::create(['guard_name' => 'api', 'name' => 'permission_super_admin']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_page_config']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_page_user']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_page_role']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_page_permission']);

		// 创建角色，并赋予权限
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_super_admin']);
		$role->givePermissionTo('permission_super_admin');
		$role->givePermissionTo('permission_page_config');
		$role->givePermissionTo('permission_page_user');
		$role->givePermissionTo('permission_page_role');
		$role->givePermissionTo('permission_page_permission');

		$role = Role::create(['guard_name' => 'api', 'name' => 'role_page_config']);
		$role->givePermissionTo('permission_page_config');
		
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_page_user']);
		$role->givePermissionTo('permission_page_user');
		
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_page_role']);
		$role->givePermissionTo('permission_page_role');
		
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_page_permission']);
		$role->givePermissionTo('permission_page_permission');
		
		// 赋予用户角色（管理员id为1）
		$user = User::where('id', 1)->first();
		$user->assignRole('role_super_admin');

    }
}
