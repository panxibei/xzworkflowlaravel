<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCirculationValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circulation_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('circulation_id')->comment('流程ID');
            $table->unsignedInteger('slot_id')->comment('slot_id');
            $table->text('field_id')->comment('field_id');
            $table->text('field_value')->comment('field_value');
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
        Schema::dropIfExists('circulation_values');
    }
}
