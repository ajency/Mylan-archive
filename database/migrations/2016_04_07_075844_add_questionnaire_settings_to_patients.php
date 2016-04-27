<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionnaireSettingsToPatients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'users', function(Blueprint $table) {
            $table->integer( 'frequency');
            $table->integer( 'grace_period');
            $table->integer( 'reminder_time');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'users', function(Blueprint $table) {
            $table->dropColumn( 'frequency' );
            $table->dropColumn( 'grace_period' );
            $table->dropColumn( 'reminder_time' );
        } );
    }
}
