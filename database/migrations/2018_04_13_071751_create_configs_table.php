<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('cfg_id');
			$table->string('cfg_name',100)->unique()->comment('配置项键名');
			$table->text('cfg_value')->nullable()->comment('配置项键值 1表示开启 0 关闭');
			$table->string('cfg_description')->nullable()->comment('配置项描述');
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
        Schema::dropIfExists('configs');
    }
}
