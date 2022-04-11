<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExecstates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('execstates', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id');
            $table->float('exec_prio', 6, 3)->default(0);            
            $table->string('exec_prio_message',250)->nullable();
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
        Schema::drop('execstates');
    }
}
