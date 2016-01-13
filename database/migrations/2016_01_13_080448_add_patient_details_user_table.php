<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPatientDetailsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'users', function(Blueprint $table) {
            $table->integer( 'age');
            $table->integer( 'patient_weight');
            $table->string( 'patient_height');
            $table->string( 'patient_is_smoker');
            $table->integer( 'patient_smoker_per_week');
            $table->string( 'patient_is_alcoholic');
            $table->integer( 'patient_alcohol_units_per_week');
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
            $table->integer( 'age');
            $table->integer( 'patient_weight');
            $table->string( 'patient_height');
            $table->string( 'patient_is_smoker');
            $table->integer( 'patient_smoker_per_week');
            $table->string( 'patient_is_alcoholic');
            $table->integer( 'patient_alcohol_units_per_week');
        } );
    }
}
