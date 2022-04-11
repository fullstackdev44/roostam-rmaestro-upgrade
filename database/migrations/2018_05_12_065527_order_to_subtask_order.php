<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderToSubtaskOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function(Blueprint $table) {
            $table->dropColumn(['order']);
        });
           
        Schema::table('tasks', function(Blueprint $table) {
            $table->smallInteger('subtask_batch')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function(Blueprint $table) {
            $table->dropColumn(['subtask_batch']);
        });
           
        Schema::table('tasks', function(Blueprint $table) {
            $table->float('order', 3, 2)->default(0);
        });
    }
}
