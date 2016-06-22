<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Session;
use Illuminate\Support\Facades\Hash;

class WelcomeController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Welcome Controller
      |--------------------------------------------------------------------------
      |
      | This controller renders the "marketing page" for the application and
      | is configured to only allow guests. Like most of the other sample
      | controllers, you are free to modify or remove it as you desire.
      |
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware( 'guest' );
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index() {
        return view( 'setup' );
    }

    public function verifyReferenceCode(Request $request)
    {
         $referenceCode = $request->input('reference_code'); 
         $user = User::where('reference_Code',$referenceCode)->first(); 

         if($user==null)
          {
              return redirect('/setup')->withErrors([
                    'reference_Code' => 'Invalid Reference Code',
                ]); 
          }
          else
          {
              Session::put('referenceCode',$referenceCode); 
              if($user['account_status'] =='created')
              {
                  
                  return redirect('/set-password');
              }
              elseif($user['account_status'] =='active')
              {
                return redirect('/login');
              }
              else
              {
                return redirect('/setup')->withErrors([
                    'account_susspended' => 'Patient account suspended',
                ]);

              }
          }
    }

    public function setPassword()
    {
        $referenceCode = Session::get('referenceCode');
        $user = User::where('reference_Code',$referenceCode)->first();  

        if($user['account_status'] !='created')
        {
          return redirect('/setup')->withErrors([
                    'reference_Code' => 'Invalid Reference Code',
                ]);
        }

        return view( 'auth.setpassword');
    }

    public function doSetup(Request $request)
    {
        $referenceCode = strtolower($request->input('reference_code'));
        $password = $request->input('password'); 
        $newpassword = getPassword($referenceCode , $password);

        $user = User::where('reference_Code',$referenceCode)->first(); 

        $user->password = Hash::make($newpassword);
        $user->account_status = 'active';
        $user->save();

        return redirect('/login');

    }

}
