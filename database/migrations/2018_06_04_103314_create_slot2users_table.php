<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlot2UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slot2users', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('slot_id')->comment('slot_id');
			$table->text('user_id')->comment('user_id');
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
        Schema::dropIfExists('slot2users');
    }
}
