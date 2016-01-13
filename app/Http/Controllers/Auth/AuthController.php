<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Auth;
use App\Hospital;
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseUser;
use \Session;

use App\Http\Controllers\Rest\UserController;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function setup()
    {

        return view('auth.user-login');
    }

    public function postLogin(Request $request)
    { 
        $referenceCode = $request->input('reference_code');
        $password = trim($request->input('password'));
        if($request->has('remember'))
            $remember = $request->input('remember');
        else
           $remember = 0;
            
        $newpassword = getPassword($referenceCode , $password);
        if (Auth::attempt(['reference_code' => $referenceCode, 'password' => $newpassword], $remember))
        {   
            if(Auth::user()->account_status=='active')
            {
                $apiKey = Auth::user()->apiKey()->first()->key;
                $installationId = 'web-'.str_random(15);

                //set parse user
                $userController = new UserController();
                $parseUser = $userController->getParseUser($referenceCode,$installationId,$apiKey);
                if($parseUser!='error')
                {
                    $projectId = Auth::user()->project_id;
                    $hospitalId = Auth::user()->hospital_id;
                    $data = $userController -> postLoginData($hospitalId,$projectId);
                    $hospitalData = $data['hospital']; 
                    $questionnaireData = $data['questionnaire']; 
                    $parseUser =json_decode($parseUser,true); 

                    $sessionToken = $parseUser['result']['sessionToken'];
                    Session::put('parseToken',$sessionToken); 

                    //if schedule not set for patient
                    /******************************/
                    if($parseUser['result']['scheduleFlag']==false)
                    {
                        $questionnaireObj = new ParseQuery("Questionnaire");
                        $questionnaire = $questionnaireObj->get($data['questionnaire']['id']);

                        $date = new \DateTime();


                        $schedule = new ParseObject("Schedule");
                        $schedule->set("questionnaire", $questionnaire);
                        $schedule->set("patient", $referenceCode);
                        $schedule->set("startDate", $date);
                        $schedule->set("nextOccurrence", $date);
                        $schedule->save();
                    }
                    
                    return redirect()->intended('dashbord');
                }
                else
                {
                    Auth::logout();
                    return redirect('/login')->withErrors([
                        'email' => 'Account inactive, contact administrator',
                    ]);
                }
                
            }
            else
            {
                Auth::logout();
                return redirect('/login')->withErrors([
                    'email' => 'Account inactive, contact administrator',
                ]);
            }
        }
        
        return redirect('login')->withErrors([
            'email' => 'The credentials you entered did not match our records. Try again?',
        ]);
    }

 

    public function getAdminLogin()
    {

        return view('auth.admin-login');
    }

    public function postAdminLogin(Request $request)
    { 
        $email = $request->input('email');
        $password = trim($request->input('password'));
        if($request->has('remember'))
            $remember = $request->input('remember');
        else
           $remember = 0;
            
        
        if (Auth::attempt(['type' => 'mylan_admin','email' => $email, 'password' => $password], $remember))
        {   
            if(Auth::user()->account_status=='active')
            {
                return redirect()->intended('admin/dashbord');
            }
            else
            {
                Auth::logout();
                return redirect('/admin/login')->withErrors([
                    'email' => 'Account inactive, contact administrator',
                ]);
            }
        }
        
        return redirect('/admin/login')->withErrors([
            'email' => 'The credentials you entered did not match our records. Try again?',
        ]);
    }

    public function getHospitalLogin($hospitalSlug)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];
        return view('auth.user-login')->with('hospital', $hospital)
                                      ->with('logoUrl', $logoUrl);
    }

    public function postHospitalLogin(Request $request,$hospitalSlug)
    { 

        $email = $request->input('email');
        $password = trim($request->input('password'));
        if($request->has('remember'))
            $remember = $request->input('remember');
        else
           $remember = 0;
            
        
        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) //'type' => 'mylan_admin',
        {   
            if(Auth::user()->account_status=='active')
            {
                return redirect()->intended($hospitalSlug.'/dashbord');
            }
            else
            {
                Auth::logout();
                return redirect($hospitalSlug.'/login')->withErrors([
                    'email' => 'Account inactive, contact administrator',
                ]);
            }
        }
        
        return redirect($hospitalSlug.'/login')->withErrors([
            'email' => 'The credentials you entered did not match our records. Try again?',
        ]);
    }

    public function getLogout()
    {  
        Auth::logout();
        Session::put('referenceCode',''); 
        $routePrefix = \Request::route()->getPrefix();
        if(str_contains($routePrefix, 'admin'))
            return redirect('admin/login');
        elseif(str_contains($routePrefix, ''))
        {
            return redirect('/');
        }
        else 
        {
            $hospitalslug = \Request::segment(1);  
            return redirect($hospitalslug.'/login');
        }
    }

 

}
