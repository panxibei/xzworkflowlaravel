<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name',100)->comment('field名称');
			$table->string('type',15)->comment('field类型');
			$table->string('bgcolor',7)->nullable()->comment('field背景色');
			$table->boolean('readonly')->default(0)->comment('field是否只读');
			$table->text('value')->nullable()->comment('field值');
			$table->string('placeholder',50)->nullable()->comment('field placeholder');
			$table->string('regexp',200)->nullable()->comment('field正则');
			$table->string('helpblock',50)->nullable()->comment('field helpblock');
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
        Schema::dropIfExists('fields');
    }
}
