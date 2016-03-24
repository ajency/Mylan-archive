<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValidateColumnToAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'attributes', function(Blueprint $table) {
            $table->string( 'validate');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'attributes', function(Blueprint $table) {
            $table->dropColumn( 'validate' );
        } );
    }
}
