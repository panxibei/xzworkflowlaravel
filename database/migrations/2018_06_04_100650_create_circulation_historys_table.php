<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCirculationHistorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circulation_historys', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('circulation_id')->comment('流程ID');
            $table->unsignedInteger('slot_id')->comment('slot_id');
            $table->unsignedInteger('user_id')->comment('user_id');
            $table->string('datetime_received')->comment('datetime_received');
            $table->string('datetime_decission')->comment('datetime_decission');
            $table->boolean('decission')->default(false)->comment('decission');
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
        Schema::dropIfExists('circulation_historys');
    }
}
