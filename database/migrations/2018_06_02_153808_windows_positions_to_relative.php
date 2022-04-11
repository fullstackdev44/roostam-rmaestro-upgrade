<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WindowsPositionsToRelative extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('windows', function (Blueprint $table) {
            $table->dropColumn(['top', 'left', 'width', 'height']);
        });
        
        Schema::table('windows', function (Blueprint $table) {
            $table->float('top',8,6);
            $table->float('left',8,6);
            $table->float('width',8,6);
            $table->float('height',8,6);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('windows', function (Blueprint $table) {
            $table->dropColumn(['top', 'left', 'width', 'height']);
        });
        
        Schema::table('windows', function (Blueprint $table) {
            $table->float('top');
            $table->float('left');
            $table->float('width');
            $table->float('height');
        });
    }
}
