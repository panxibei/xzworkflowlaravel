<?php

use Illuminate\Database\Seeder;

use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$nowtime = date("Y-m-d H:i:s",time());
		
        //
		User::truncate();
		
		// DB::table('configs')->insert(array (
		User::insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'user1',
                'email' => 'user1@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'user2',
                'email' => 'user2@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'user3',
                'email' => 'user3@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'user4',
                'email' => 'user4@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'user5',
                'email' => 'user5@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'user6',
                'email' => 'user6@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'user7',
                'email' => 'user7@xz.com',
                'password' => '123',
                'login_time' => time(),
                'login_ip' => '127.0.0.1',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
        ));
	}
}
