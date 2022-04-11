<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnhanceExecdata extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('execdatas', function (Blueprint $table) {
            $table->float('urgency', 4, 2)->default(0);
            $table->float('deadline_state', 4, 2)->default(0);
            $table->float('attention_state', 4, 2)->default(0);
            $table->float('duration_state', 4, 2)->default(0);
            $table->float('path_attention_state', 4, 2)->default(0);
            $table->float('path_duration_state', 4, 2)->default(0);
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
            $table->dropColumn(['deadline_state', 'attention_state', 'duration_state', 'path_attention_state', 'path_duration_state', 'urgency']);
        });
    }
}
