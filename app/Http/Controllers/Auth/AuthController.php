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
                return redirect()->intended('patient/dashbord');
            }
            else
            {
                Auth::logout();
                return redirect('/patient/login')->withErrors([
                    'email' => 'Account inactive, contact administrator',
                ]);
            }
        }
        
        return redirect('/patient/login')->withErrors([
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

    public function getHospitalLogin($hospitalId)
    {
        $hospital = Hospital::find($hospitalId)->toArray(); 
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];
        return view('auth.user-login')->with('hospital', $hospital)
                                      ->with('logoUrl', $logoUrl);
    }

    public function postHospitalLogin(Request $request,$hospitalId)
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
                return redirect()->intended('hospital/'.$hospitalId.'/dashbord');
            }
            else
            {
                Auth::logout();
                return redirect('hospital/'.$hospitalId.'/login')->withErrors([
                    'email' => 'Account inactive, contact administrator',
                ]);
            }
        }
        
        return redirect('hospital/'.$hospitalId.'/login')->withErrors([
            'email' => 'The credentials you entered did not match our records. Try again?',
        ]);
    }

 

}
