<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IdsToUnderscoreIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // execstatuses
        Schema::table('execstatuses', function (Blueprint $table) {
            $table->dropColumn(['id']);
        });    
        Schema::table('execstatuses', function (Blueprint $table) {
            $table->increments('_id');
        });    
        
        // execdatas
        Schema::table('execdatas', function (Blueprint $table) {
            $table->dropColumn(['id']);
        });    
        Schema::table('execdatas', function (Blueprint $table) {
            $table->increments('_id');
        });    
        
        // taskwaits
        Schema::table('taskwaits', function (Blueprint $table) {
            $table->dropColumn(['id']);
        });    
        Schema::table('taskwaits', function (Blueprint $table) {
            $table->increments('_id');
        });    
        
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // execstatuses
        Schema::table('execstatuses', function (Blueprint $table) {
            $table->dropColumn(['_id']);
        });
        Schema::table('execstatuses', function (Blueprint $table) {
            $table->increments('id');
        });
        
        // execdatas
        Schema::table('execdatas', function (Blueprint $table) {
            $table->dropColumn(['_id']);
        });
        Schema::table('execdatas', function (Blueprint $table) {
            $table->increments('id');
        });

        // taskwaits
        Schema::table('taskwaits', function (Blueprint $table) {
            $table->dropColumn(['_id']);
        });
        Schema::table('taskwaits', function (Blueprint $table) {
            $table->increments('id');
        });
        
    }
}
