<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'hospital_id' )->unsigned();
            $table->integer( 'department_id' )->unsigned();
            $table->integer( 'user_id' )->unsigned();
            $table->string( 'questionniare_id' );
            $table->dateTime( 'start_date' );
            $table->dateTime( 'due_date' );
            $table->timestamps();

            $table->foreign( 'hospital_id' )
                    ->references( 'id' )
                    ->on( 'hospital' )
                    ->onDelete( 'cascade' );

            $table->foreign( 'department_id' )
                    ->references( 'id' )
                    ->on( 'department' )
                    ->onDelete( 'cascade' );

            $table->foreign( 'user_id' )
                    ->references( 'id' )
                    ->on( 'users' )
                    ->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hospital_users');
    }
}
