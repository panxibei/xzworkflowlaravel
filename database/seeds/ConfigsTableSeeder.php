<?php

use Illuminate\Database\Seeder;

use App\Models\Config;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$nowtime = date("Y-m-d H:i:s",time());
		
        // DB::table('configs')->delete();
		// Let's clear the users table first
		Config::truncate();
		
		// DB::table('configs')->insert(array (
		Config::insert(array (
            0 => 
            array (
                'cfg_id' => 1,
                'cfg_name' => 'SITE_TITLE',
                'cfg_value' => 'xzWorkFlow 2017',
                'cfg_description' => '站点名称',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'cfg_id' => 2,
                'cfg_name' => 'SITE_VERSION',
                'cfg_value' => '1804.13.0.1550',
				'cfg_description' => '站点版本号',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'cfg_id' => 3,
                'cfg_name' => 'SITE_COPYRIGHT',
                'cfg_value' => '© 2013-2018 xizhisoft.com All Rights Reserved.',
				'cfg_description' => '站点版权信息',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'cfg_id' => 4,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_USER',
                'cfg_value' => '1',
				'cfg_description' => '用户页每页记录数',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'cfg_id' => 5,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_ROLE',
                'cfg_value' => '1',
				'cfg_description' => '角色页每页记录数',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'cfg_id' => 6,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_PERMISSION',
                'cfg_value' => '1',
				'cfg_description' => '权限页每页记录数',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'cfg_id' => 7,
                'cfg_name' => 'EXPORTS_EXTENSION_TYPE',
                'cfg_value' => 'xlsx',
				'cfg_description' => '导出文件扩展名（xlsx、xls、csv）',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'cfg_id' => 8,
                'cfg_name' => 'FILTERS_USER_NAME',
                'cfg_value' => '',
				'cfg_description' => '过滤器，用户名称',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'cfg_id' => 9,
                'cfg_name' => 'FILTERS_USER_EMAIL',
                'cfg_value' => '',
				'cfg_description' => '过滤器，邮箱',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'cfg_id' => 10,
                'cfg_name' => 'FILTERS_USER_LOGINTIME_DATEFROM',
                'cfg_value' => '',
				'cfg_description' => '过滤器，登录开始时间',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'cfg_id' => 11,
                'cfg_name' => 'FILTERS_USER_LOGINTIME_DATETO',
                'cfg_value' => '',
				'cfg_description' => '过滤器，登录结束时间',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
        ));
    }
}
