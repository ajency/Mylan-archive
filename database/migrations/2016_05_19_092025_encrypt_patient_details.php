<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;
use App\PatientClinicVisit;
use App\PatientMedication;


class EncryptPatientDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::where('type','patient')->get()->each( function($patient) {
            $patientIsSmoker = $patient->patient_is_smoker; 
            $projectAttributes = $patient->project_attributes;

            $patient->patient_is_smoker = $patientIsSmoker;
            $patient->project_attributes = $projectAttributes;
            $patient->save();
           
        });

        PatientClinicVisit::all()->each( function($patient) {
            $note = $patient->note;
            
            $patient->note = $note;
            $patient->save();
           
        });


        PatientMedication::all()->each( function($patient) {
            $medication = $patient->medication;
             
            $patient->medication = $medication;
            $patient->save();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
