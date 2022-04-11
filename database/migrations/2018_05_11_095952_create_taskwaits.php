<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskwaits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskwaits', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id');
            $table->string('task_exec_state',20)->nullable();
            $table->string('for_event',20)->nullable(); 
            $table->integer('for_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('taskwaits');
    }
}
