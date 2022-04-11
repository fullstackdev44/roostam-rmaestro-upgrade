<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLongestPaths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('longestpaths', function(Blueprint $table) {
            $table->increments('_id');
            $table->integer('task_id_from');
            $table->integer('task_id_to');
            $table->char('path_type',1);
            $table->integer('index');
            $table->integer('index_id');
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
        Schema::drop('longestpaths');
    }
}
