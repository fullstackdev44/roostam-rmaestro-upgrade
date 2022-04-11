<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesksAndWindows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desks', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id');
            $table->binary('desk_image')->nullable()->default(null);
            $table->timestamps();
        });
        
        Schema::create('windows', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('desk_id');
            $table->string('title');
            $table->float('top');
            $table->float('left');
            $table->float('width');
            $table->float('height');
            $table->char('type',10);
            $table->text('content');
            $table->timestamps();
            $table->boolean('is_default')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('desks');
        Schema::drop('windows');
    }
}
