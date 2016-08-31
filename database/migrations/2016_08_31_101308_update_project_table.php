<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('reviewed_no_action');
            $table->text('reviewed_call_done');
            $table->text('reviewed_appointment_fixed');
            $table->text('unreviewed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function($table) {
            $table->dropColumn('reviewed_no_action');
            $table->dropColumn('reviewed_call_done');
            $table->dropColumn('reviewed_appointment_fixed');
            $table->dropColumn('unreviewed');
        });
    }
}
