<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PropertiesToSchedulingData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['info', 'deadline', 'importance', 'duration']); 
        }); 

  
        Schema::table('properties', function (Blueprint $table) {
            $table->float('importance', 3, 2)->default(0);
            $table->float('urgency', 3, 2)->default(0);
            $table->dateTime('deadline');
            $table->float('deadline_range', 5, 2)->default(0);
            $table->float('attention_average', 8, 2)->default(30);
            $table->float('attention_next', 8, 2)->default(5);
            $table->float('duration_average', 8, 2)->default(4320);
            $table->float('duration_min', 8, 2)->default(60);
         }); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['importance', 'urgency', 'deadline', 'deadline_range',
                'attention_average','attention_next','duration_average','duration_min']);
        }); 
        
        Schema::table('properties', function (Blueprint $table) {
            $table->string('info',250)->nullable();
            $table->date('deadline');
            $table->integer('importance');
            $table->integer('duration');
        }); 
        
    }
}
