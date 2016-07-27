<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Crypt;

class PatientClinicVisit extends Model
{
        protected $fillable = ['date_visited', 'note'];

     //    public function getNoteAttribute( $value ) { 
     //    	return Crypt::decrypt( $value );
    	// }

    	public function setNoteAttribute( $value ) { 
        	$this->attributes['note'] = Crypt::encrypt( $value );
    	}
}
