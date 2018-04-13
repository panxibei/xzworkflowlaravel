<?php

use Illuminate\Database\Seeder;

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
		
        DB::table('configs')->delete();
		
		DB::table('configs')->insert(array (
            0 => 
            array (
                'cfg_id' => 1,
                'cfg_name' => 'SITE_TITLE',
                'cfg_value' => 'xzWorkFlow 2017',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'cfg_id' => 2,
                'cfg_name' => 'SITE_VERSION',
                'cfg_value' => '1804.13.0.1550',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
        ));
    }
}
