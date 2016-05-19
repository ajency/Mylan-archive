<?php

namespace App;

use Crypt;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Encryption\DecryptException;


class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function apiKey() {
        return $this->hasOne( 'Chrisbjr\ApiGuard\Models\ApiKey');
    }

    public function devices() {
        return $this->hasMany( 'App\UserDevice');
    }

    public function access() {
        return $this->hasMany( 'App\UserAccess' );
    }

    public function project() {
        return $this->belongsTo( 'App\Projects' );
    }

    public function hospital() {
        return $this->belongsTo( 'App\Hospital' );
    }

    public function medications() {
        return $this->hasMany( 'App\PatientMedication' );
    }

    public function clinicVisit() {
        return $this->hasMany( 'App\PatientClinicVisit' );
    }


    // public function getPatientIsSmokerAttribute( $value ) { 
    //     if($this->attributes['type']=="patient")
    //     {
    //         $value = Crypt::decrypt( $value );
    //     }

    //     return $value;
    // }

    public function setPatientIsSmokerAttribute( $value ) {  
        $this->attributes['patient_is_smoker'] = Crypt::encrypt( $value );
    }

    // public function getProjectAttributesAttribute( $value ) { 
    //     if($this->attributes['type']=="patient")
    //     {
    //         $value = Crypt::decrypt( $value );
    //     }

    //     return $value;
    // }

    public function setProjectAttributesAttribute( $value ) {  
        $this->attributes['project_attributes'] = Crypt::encrypt( $value );
    }

    public function toArray()
    {
        $data = parent::toArray();
        if($data['type']!='patient')
        {
            $userAccess =  $this->access()->get()->toArray();
            $data['access'] = $userAccess;
        }
        else
        {
            $data['project_attributes'] = unserialize($data['project_attributes']);
        }

        return $data;
    }
}
