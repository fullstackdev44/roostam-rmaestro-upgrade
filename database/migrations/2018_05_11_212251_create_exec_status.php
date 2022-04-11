<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExecStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('execstatuses', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id');
            $table->string('exec_state',20)->nullable()->default('exec_notexecuting');
            $table->boolean('is_active_action')->default(false); 
            $table->timestamps();
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['exec_state']);
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_executing')->default(false); 
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('execstatuses');
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['is_executing']);
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('exec_state')->nullable()->default(1);
        });

    }
}
