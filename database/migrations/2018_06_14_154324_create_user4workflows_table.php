<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUser4workflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user4workflows', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('user_id')->comment('user_id');
			$table->integer('rights')->default(0)->comment('用户访问流程权限');
			$table->text('substitute_user_id')->nullable()->default(null)->comment('代理人用户ID');
			$table->integer('substitute_time')->default(480)->comment('代理人所需时间');
            $table->timestamps();
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
        Schema::dropIfExists('user4workflows');
    }
}
