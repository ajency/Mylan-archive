<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get( '/', 'WelcomeController@index' );

/********API********/
Route::group( ['prefix' => 'api/v1', 'middleware' => ['api_auth']], function() {
    Route::post( 'user/dosetup', 'Rest\UserController@doSetup' );
    Route::post( 'user/login', 'Rest\UserController@doLogin' );
    Route::post( 'user/setpassword', 'Rest\UserController@setPassword' );
} );

/**
 * Auth and forgot password route
 */
Route::get('patient/login', 'Auth\AuthController@getLogin');
Route::post('patient/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::get('hospital/{id}/login', 'Auth\AuthController@getHospitalLogin');
Route::post('hospital/{id}/login', 'Auth\AuthController@postHospitalLogin');
Route::get('hospital/{id}/logout', 'Auth\AuthController@getLogout');

Route::get('admin/login', 'Auth\AuthController@getAdminLogin');
Route::post('admin/login', 'Auth\AuthController@postAdminLogin');
Route::get('admin/logout', 'Auth\AuthController@getLogout');

/*****Admin***/
Route::group( ['prefix' => 'admin', 'middleware' => ['auth','permission']], function() {
Route::get( '/', 'Admin\UserController@dashbord' );
Route::get( '/dashbord', 'Admin\UserController@dashbord' );
Route::resource( 'hospitals', 'Admin\HospitalController' );
Route::resource( 'users', 'Admin\UserController' );
Route::resource( 'user-access', 'Admin\UserAccessController' );


Route::post( 'hospital/{hospital}/uploadlogo', 'Admin\HospitalController@uploadLogo' );
Route::post( 'hospital/{hospital}/deletelogo', 'Admin\HospitalController@deleteLogo' );

});

/*****PATIENT***/
Route::group( ['prefix' => 'patient', 'middleware' => ['auth']], function() {
Route::get( '/', 'Patient\PatientController@index' );
Route::get( '/dashbord', 'Patient\PatientController@index' );

});

Route::group( ['prefix' => '{hospitalslug}'  , 'middleware' => ['auth','hospital.permission']], function() {
Route::get( '/', 'Hospital\HospitalController@show' );
Route::get( '/dashbord', 'Hospital\HospitalController@show' );
Route::resource( 'patients', 'Hospital\PatientController' );
Route::resource( 'submissions', 'Hospital\SubmissionController' );
Route::resource( 'projects', 'Hospital\ProjectController' );
Route::resource( 'users', 'Hospital\UserController' );

Route::get( 'patients/{id}/submission-reports', 'Hospital\PatientController@getSubmissionReports' );
Route::post( 'patients/{id}/validatereferncecode', 'Hospital\PatientController@validateRefernceCode' );
});

