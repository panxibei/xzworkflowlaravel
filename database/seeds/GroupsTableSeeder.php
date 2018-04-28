<?php

use Illuminate\Database\Seeder;

use App\Models\Group;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$nowtime = date("Y-m-d H:i:s",time());
		
		Group::truncate();
		
		Group::insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '管理员组',
                'rules' => '1,2,3,4,5,6,13,14',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '普通用户组',
                'rules' => '1,2,13,14,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '游客组',
                'rules' => '1',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
        ));
	}
}
