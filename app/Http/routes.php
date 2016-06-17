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
Route::get( '/setup', 'WelcomeController@index' );
Route::post( '/setup', 'WelcomeController@verifyReferenceCode' );
Route::get( '/set-password', 'WelcomeController@setPassword' );
Route::post( '/dosetup', 'WelcomeController@doSetup' );

/********API********/
Route::group( ['prefix' => 'api/v1', 'middleware' => ['api_auth']], function() {
    Route::post( 'user/dosetup', 'Rest\UserController@doSetup' );
    Route::post( 'user/login', 'Rest\UserController@doLogin' );
    Route::post( 'user/setpassword', 'Rest\UserController@setPassword' );
    Route::post( 'user/contactus', 'Rest\UserController@contactUs' );
} );

/********API********/
Route::group( ['prefix' => 'api/v2', 'middleware' => ['api_auth']], function() {
    Route::post( 'project/{id}/clear-cache', 'Project\ProjectController@clearCache' );
    
} );

Route::group( ['prefix' => 'api/v3'], function() {
    Route::get('ajaxCApi', 'Rest\apiDataController@apiLogin');
	Route::get('fillproject', 'Rest\apiDataController@projectList');
	Route::get('mapping-data', 'Rest\apiDataController@mappingList');
    
} );



/**
 * Auth and forgot password route
 */

 
Route::get('login', 'Auth\AuthController@getLogin');
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getPatientLogout');

Route::get('admin/login', 'Auth\AuthController@getAdminLogin');
Route::post('admin/login', 'Auth\AuthController@postAdminLogin');
Route::get('admin/logout', 'Auth\AuthController@getLogout');

Route::get('{hospitalslug}/login', 'Auth\AuthController@getHospitalLogin');
Route::post('{hospitalslug}/login', 'Auth\AuthController@postHospitalLogin');
Route::get('{hospitalslug}/logout', 'Auth\AuthController@getHospitalLogout');

Route::get('{hospitalslug}/{projectslug}/login', 'Auth\AuthController@getProjectLogin');
Route::post('{hospitalslug}/{projectslug}/login', 'Auth\AuthController@postProjectLogin');
Route::get('{hospitalslug}/{projectslug}/logout', 'Auth\AuthController@getProjectLogout');


/*****PATIENT***/
Route::group( ['middleware' => ['auth']], function() {
//Route::get( '/', 'Patient\PatientController@index' );
Route::get( '/dashboard', 'Patient\PatientController@index' );

});

/*****Admin***/
Route::group( ['prefix' => 'admin', 'middleware' => ['auth']], function() {
 
Route::get('/login-links', ['middleware' => 'permission', 'uses' => 'Admin\UserController@loginLinks']);
});

Route::group( ['prefix' => 'admin', 'middleware' => ['auth','mylan.permission']], function() {
Route::get( '/', 'Admin\UserController@dashboard' );
Route::get( '/dashboard', 'Admin\UserController@dashboard' );
Route::resource( 'hospitals', 'Admin\HospitalController' );
Route::resource( 'users', 'Admin\UserController' );
Route::resource( 'user-access', 'Admin\UserAccessController' );

Route::get( 'hospital/{hospital}/patients', 'Admin\HospitalController@getHospitalPatients' );

Route::post( 'hospital/{hospital}/uploadlogo', 'Admin\HospitalController@uploadLogo' );
Route::post( 'hospital/{hospital}/deletelogo', 'Admin\HospitalController@deleteLogo' );
Route::post( 'users/{id}/authuseremail', 'Admin\UserController@authUserEmail' );

Route::get( 'changepassword', 'Admin\UserController@changePassword' );
Route::post( 'changepassword', 'Admin\UserController@updateUserPassword' );

Route::post( 'patients/{id}/resetpassword', 'Project\PatientController@resetPatientPassword' );


});


/*****Hospital***/ //
Route::group( ['prefix' => '{hospitalslug}'  , 'middleware' => ['auth','hospital.permission']], function() {
 Route::get( '/', 'Hospital\HospitalController@show' );
// Route::get( '/dashbord', 'Hospital\HospitalController@show' );
Route::resource( 'projects', 'Hospital\ProjectController' );
Route::resource( 'attributes', 'Hospital\AttributeController' );
Route::resource( 'users', 'Hospital\UserController' );
Route::post( 'users/{id}/authuseremail', 'Hospital\UserController@authUserEmail' );
Route::resource( 'user-access', 'Admin\UserAccessController' );
Route::delete( 'delete-user-access/{id}', 'Admin\UserAccessController@deleteProjectAccess' );

Route::get( 'changepassword', 'Hospital\UserController@changePassword' );
Route::post( 'changepassword', 'Hospital\UserController@updateUserPassword' );

});

/*****project***/ //
Route::get( 'project/flush-cache-memory', 'Project\ProjectController@flushCacheMemory' );

Route::group( ['prefix' => '{hospitalslug}/{projectslug}'  , 'middleware' => ['auth','project.permission']], function() {
Route::get( '/', 'Project\ProjectController@show' );
Route::get( '/dashboard', 'Project\ProjectController@show' );
Route::resource( 'patients', 'Project\PatientController' );
Route::resource( 'submissions', 'Project\SubmissionController' );
Route::resource( 'projects', 'Project\ProjectController' );

Route::get( 'flags', 'Project\SubmissionController@getSubmissionFlags' );
Route::get( 'submission-notifications', 'Project\SubmissionController@getSubmissionNotifications' );
Route::post( 'submissions/{id}/updatesubmissionstatus', 'Project\SubmissionController@updateSubmissionStatus' );
Route::get( 'reports', 'Project\ProjectController@reports' );

Route::get( 'patients/{id}/base-line-score/list', 'Project\PatientController@getpatientBaseLines' );
Route::get( 'patients/{id}/base-line-score/{responseId}', 'Project\PatientController@showpatientBaseLineScore' );
Route::get( 'patients/{id}/base-line-score-edit', 'Project\PatientController@getpatientBaseLineScore' );
Route::post( 'patients/{id}/base-line-score-edit', 'Project\PatientController@setPatientBaseLineScore' );
Route::get( 'patients/{id}/submissions', 'Project\PatientController@getPatientSubmission' );
Route::get( 'patients/{id}/flags', 'Project\PatientController@getPatientFlags' );
Route::get( 'patients/{id}/patient-reports', 'Project\PatientController@getPatientReports' );
Route::post( 'patients/{id}/validatereferncecode', 'Project\PatientController@validateRefernceCode' );
Route::get( 'patients/{id}/submission-notifications', 'Project\PatientController@getSubmissionNotifications' );
Route::get( 'patients/{id}/patient-devices', 'Project\PatientController@getPatientDevices' );

Route::get( '/getsubmissionlist', 'Project\ProjectController@getSubmissionList' );
Route::get( '/getpatientsummarylist', 'Project\ProjectController@getPatientSummaryList' );
Route::get( '/notifications', 'Project\ProjectController@getNotifications' );
Route::get( '/alert-setting', 'Project\ProjectController@alertSetting' );
Route::post( '/alert-setting', 'Project\ProjectController@saveAlertSetting' );
Route::delete( 'delete-alert-setting/{id}', 'Project\ProjectController@deleteAlertSettings' );

Route::get( '/questionnaire-setting', 'Project\QuestionnaireController@questionnaireSetting' );
Route::post( '/store-questionnaire-setting', 'Project\QuestionnaireController@storeQuestionnaireSetting' );
Route::post( '/update-questionnaire-setting', 'Project\QuestionnaireController@saveQuestionnaireSetting' );
Route::get( '/configure-questions/{id}', 'Project\QuestionnaireController@configureQuestions' );
Route::post( '/configure-questions/{id}', 'Project\QuestionnaireController@StoreQuestions' );
Route::delete( 'delete-question/{id}', 'Project\QuestionnaireController@deleteQuestion' );
Route::delete( 'delete-option/{id}', 'Project\QuestionnaireController@deleteOption' );
Route::get( '/order-questions/{id}', 'Project\QuestionnaireController@getQuestionsOrder' );
Route::post( '/order-questions/{id}', 'Project\QuestionnaireController@setQuestionsOrder' );
Route::get( '/questions-summary/{id}', 'Project\QuestionnaireController@getQuestionsSummary' );

Route::get( 'changepassword', 'Project\UserController@changePassword' );
Route::post( 'changepassword', 'Project\UserController@updateUserPassword' );
});

