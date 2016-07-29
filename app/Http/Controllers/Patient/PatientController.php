<?php

namespace App\Http\Controllers\Patient;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Http\Controllers\Rest\UserController;
use \Session;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patientId = Auth::user()->id;
        $user = User::find($patientId);  

        $referenceCode = $user['reference_code'];
        $projectId = $user['project_id'];
        $hospitalId = $user['hospital_id'];
        $parseToken = Session::get('parseToken');

        $userController = new UserController();
        $data = $userController ->postLoginData($hospitalId,$projectId);
        $hospitalData = $data['hospital']; 
        $questionnaireData = $data['questionnaire']; 
   
        return view('patient.dashbord')->with('referenceCode', $referenceCode)
                                       ->with('parseToken', $parseToken)
                                       ->with('hospital', $hospitalData)
                                       ->with('questionnaire', $questionnaireData);
    }

    public function testie(){
        echo "here";
        echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";
        // $browser = get_browser(null, true);
        // print_r($browser);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
