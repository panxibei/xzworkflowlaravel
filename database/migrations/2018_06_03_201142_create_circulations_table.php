<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCirculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circulations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('guid',38)->unique()->comment('流程GUID');
            $table->string('name')->comment('流程名称');
            $table->unsignedInteger('template_id')->comment('template_id');
            $table->unsignedInteger('mailinglist_id')->comment('MailingList');
            $table->unsignedInteger('slot2user_id')->comment('slot_2_user_id');
            $table->text('slot_id')->comment('slot_id');
            $table->text('user_id')->comment('user_id');
            $table->string('current_station')->comment('当前站点');
            $table->string('creator')->comment('创建者');
            $table->timestamp('todo_time')->comment('todo时间');
            $table->string('progress', 4)->comment('进度');
            $table->string('description')->nullable()->comment('描述');
            $table->boolean('is_archived')->default(false)->comment('是否归档');
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
        Schema::dropIfExists('circulations');
    }
}
