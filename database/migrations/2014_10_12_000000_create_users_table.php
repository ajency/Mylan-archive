<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'name' );
            $table->string( 'email' );
            $table->string( 'phone' ,12);
            $table->string('type');
            $table->string('reference_code', 8);
            $table->string('password',60);
            $table->string('account_status');
            $table->string('project_access');
            $table->string('hospital_id');
            $table->string('project_id');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
