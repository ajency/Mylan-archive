<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Crypt;

class PatientMedication extends Model
{
    protected $fillable = ['user_id', 'medication'];

    // public function getMedicationAttribute( $value ) { 
    //     return Crypt::decrypt( $value );
    // }

    public function setMedicationAttribute( $value ) { 
        $this->attributes['medication'] = Crypt::encrypt( $value );
    }
}
