<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailinglistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailinglists', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name')->comment('mailinglist名称');
			$table->unsignedInteger('template_id')->comment('template_id');
			$table->boolean('isdefault')->default(0)->comment('isdefault');
			$table->text('slot2user_id')->nullable()->comment('slot2user_id');
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
        Schema::dropIfExists('mailinglists');
    }
}
