<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'user_id' )->unsigned();
            $table->string('device_type');
            $table->string('device_identifier');
            $table->string('device_os');
            $table->string('access_type');
            $table->timestamps();

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
        Schema::drop('user_devices');
    }
}
