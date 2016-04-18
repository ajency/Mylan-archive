<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;
use Parse\ParseObject;
use Parse\ParseQuery;

class CreateBaselineFlagColumnForPatients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'users', function(Blueprint $table) {
            $table->string( 'baseline_set')->default( 'no' );
        } );

        // User::where('type','patient')->get()->each( function($patient) {
        //     $referenceCode = $patient->reference_code;
        //     $responseQry = new ParseQuery("Response");
        //     $responseQry->equalTo("patient", $referenceCode); 
        //     $responseQry->equalTo("status", 'base_line'); 
        //     $response = $responseQry->first();  dd($response);

        //     $baselineSet = (empty($response))?'no':'yes';
        //     $patient->baseline_set = $baselineSet;
        //     $patient->save();
           
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'users', function(Blueprint $table) {
            $table->string( 'baseline_set');
        } );
    }
}
