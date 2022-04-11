<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExecStatesEnhancements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('execstates', function (Blueprint $table) {
            $table->dropColumn(['exec_prio_message']);
        });
        
        Schema::table('execstates', function (Blueprint $table) {
            $table->text('exec_prio_message')->nullable();            
            $table->text('exec_recommendation')->nullable();
            $table->integer('suggested_delay')->nullable();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('execstates', function (Blueprint $table) {
            $table->dropColumn(['exec_prio_message','exec_recommendation','suggested_delay']);
        });
        
        Schema::table('execstates', function (Blueprint $table) {
            $table->string('exec_prio_message',250)->nullable();            
        });
    }

}
