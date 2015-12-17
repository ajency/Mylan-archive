<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('logo');
            $table->string('email');
            $table->string('phone');
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('city');
            $table->string('country');
            $table->string('postal_code');
            $table->string('website');
            $table->string('primary_phone');
            $table->string('primary_email');
            $table->string('contact_person_name');
            $table->string('url_slug')->unique();
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
        Schema::drop('hospitals');
    }
}
