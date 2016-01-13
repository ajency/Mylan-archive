<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientClinicVisit extends Model
{
        protected $fillable = ['date_visited', 'note'];
}
