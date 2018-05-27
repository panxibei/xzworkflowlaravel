<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',32)->unique();
            $table->string('email',36)->unique();
            $table->string('password');
			$table->timestamp('login_time')->default(null)->comment('登录时间');
			$table->string('login_ip',15)->default(null)->comment('登录ip');
			$table->integer('login_counts')->default(0)->comment('登录次数');
            $table->rememberToken();
            $table->timestamps();
			$table->softDeletes();
			$table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
