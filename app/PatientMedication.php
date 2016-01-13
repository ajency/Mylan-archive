<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatientMedication extends Model
{
    protected $fillable = ['user_id', 'medication'];
}
