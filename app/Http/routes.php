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

/**
 * Auth and forgot password route
 */
Route::get('patient/login', 'Auth\AuthController@getLogin');
Route::post('patient/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::get('hospital/login', 'Auth\AuthController@getUserLogin');
Route::post('hospital/login', 'Auth\AuthController@postUserLogin');
Route::get('hospital/logout', 'Auth\AuthController@getLogout');

Route::get('admin/login', 'Auth\AuthController@getAdminLogin');
Route::post('admin/login', 'Auth\AuthController@postAdminLogin');
Route::get('admin/logout', 'Auth\AuthController@getLogout');

/*****User***/
Route::group( ['prefix' => 'admin', 'middleware' => ['auth']], function() {
Route::get( '/', 'Admin\UserController@dashbord' );
Route::get( '/dashbord', 'Admin\UserController@dashbord' );
Route::resource( 'hospitals', 'Admin\HospitalController' );
Route::resource( 'patients', 'Admin\UserController' );
Route::resource( 'submissions', 'Admin\SubmissionController' );

Route::post( 'hospital/{hospital}/media/uploadlogo', 'Admin\MediaController@uploadLogo' );

});

/*****PATIENT***/
Route::group( ['prefix' => 'patient', 'middleware' => ['auth']], function() {
Route::get( '/', 'Patient\PatientController@index' );
Route::get( '/dashbord', 'Patient\PatientController@index' );

});


/********API********/
Route::group( ['prefix' => 'api/v1', 'middleware' => ['api_auth']], function() {
    Route::post( 'user/dosetup', 'Rest\UserController@doSetup' );
    Route::post( 'user/login', 'Rest\UserController@doLogin' );
    Route::post( 'user/setpassword', 'Rest\UserController@setPassword' );
} );
