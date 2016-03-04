<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\UserAccess;
use App\Hospital;
use \Mail;
 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $users = User::where('type','hospital_user')->orderBy('created_at')->get()->toArray();
       

        return view('admin.users-list')->with('active_menu', 'users')
                                          ->with('users', $users); 
    }

    public function dashboard()
    {  
        return view('admin.dashbord')->with('active_menu', 'dashbord');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  
 
        $roles = getRoles();
        $hospitals = Hospital:: all()->toArray(); 
        return view('admin.user-add')->with('active_menu', 'users')
                                    ->with('hospitals',$hospitals)
                                    ->with('roles',$roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $password = randomPassword();

        $user = new User;
        $name =  ucfirst($request->input('name'));
        $email = $request->input('email');
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->phone = $request->input('phone');     
        $user->type = 'hospital_user'; 
        $user->account_status = 'active'; 
        $user->has_all_access = ($request->has('has_all_access'))?'yes':'no';
        $user->save(); 
        $userId = $user->id;

        $hospitalIds = $request->input('hospital');
        $hospitalUrlStr = '';

        if(!empty($hospitalIds))
        {
            foreach ($hospitalIds as $key => $hospitalId) {
                 if($hospitalId=='')
                    continue;

                $access = $request->input('access_'.$key);

                $userAccess = new UserAccess;
                $userAccess->object_type = 'hospital' ; 
                $userAccess->object_id = $hospitalId; 
                $userAccess->user_id = $userId; 
                $userAccess->access_type = $access; 
                $userAccess->save();
            }
 

          $hospitals = Hospital:: whereIn('id',$hospitalIds)->get()->toArray(); 


          foreach ($hospitals as $hospital) {
              $hospitalName = $hospital['name'];
              $urlSlug = $hospital['url_slug'];

              $hospitalUrlStr .= $hospitalName .' : '.url().'/'.$urlSlug . ' <br>';
          }
            
        }

        
        
        
        $data =[];
        $data['name'] = $name;
        $data['email'] = $email;
        $data['password'] = $password;
        $data['loginUrls'] = $hospitalUrlStr;
 
        Mail::send('admin.registermail', ['user'=>$data], function($message)use($data)
        {  
            $message->to($data['email'], $data['name'])->subject('Welcome to Mylan!');
        });
        
         
        return redirect(url('/admin/users/' . $userId . '/edit'));
        
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
        $hospitals = Hospital:: all()->toArray();
        $user = User::find($id)->toArray();
        $userAccess = UserAccess::where(['user_id'=>$id,'object_type'=>'hospital'])->get()->toArray();  
        
        $mylanUserAccess['access_type'] = 'view';
        $mylanUserAccess['id'] = '';
 

        return view('admin.user-edit')->with('active_menu', 'users')
                                          ->with('hospitals', $hospitals)
                                          ->with('userAccess', $userAccess)
                                          ->with('mylanUserAccess', $mylanUserAccess)
                                          ->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {
        $user = User::find($userId);
        $name =  ucfirst($request->input('name'));
        $user->name = $name;
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');     
        $user->has_all_access = ($request->has('has_all_access'))?'yes':'no';
        $user->save(); 

        $hospitals = $request->input('hospital');
        if(!empty($hospitals))
        {
            foreach ($hospitals as $key => $hospital) {

                if($hospital=='')
                    continue;

                $access = $request->input('access_'.$key);
                $user_access = $request->input('user_access')[$key];

                if($user_access!='')
                {
                    $userAccess = UserAccess::find($user_access);
                    $userAccess->access_type = $access; 
                    $userAccess->save();
                }
                else
                {
                    $userAccess = new UserAccess;
                    $userAccess->object_type = 'hospital' ; 
                    $userAccess->object_id = $hospital; 
                    $userAccess->user_id = $userId; 
                    $userAccess->access_type = $access; 
                    $userAccess->save();
                }
                
            }
            
        }
        
        
        return redirect(url('/admin/users/' . $userId . '/edit'));
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
