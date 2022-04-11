<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPathsToExecData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('execdatas', function (Blueprint $table) {
            $table->text('attention_path_to_deadline');
            $table->text('duration_path_to_deadline');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('execdatas', function (Blueprint $table) {
            $table->dropColumn(['attention_path_to_deadline', 'duration_path_to_deadline']);
        });
    }
}
