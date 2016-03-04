<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\UserAccess;
use App\Hospital;
use App\Projects;
use \Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($hospitalSlug)
    {
       $users = User::where('type','project_user')->orderBy('created_at')->get()->toArray();
       $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
       $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        return view('hospital.users-list')->with('active_menu', 'users')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('users', $users); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($hospitalSlug)
    {  
        
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];
        $projects = Projects:: all()->toArray(); 

        return view('hospital.user-add')->with('active_menu', 'users')
                                    ->with('projects', $projects)
                                    ->with('hospital', $hospital)
                                    ->with('logoUrl', $logoUrl);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$hospitalSlug)
    {
        $password = randomPassword();

        $user = new User;
        $name =  ucfirst($request->input('name'));
        $user->name = $name;
        $user->email = $request->input('email');
        $user->password = Hash::make($password);
        $user->phone = $request->input('phone');     
        $user->type = 'project_user'; 
        $user->account_status = 'active'; 
        $user->has_all_access = ($request->has('has_all_access'))?'yes':'no';
        $user->save(); 
        $userId = $user->id;

        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();
        
        $projects = $request->input('projects');
        if(!empty($projects))
        {
            foreach ($projects as $key => $project) {
                 if($project=='')
                    continue;

                $access = $request->input('access_'.$key);

                $userAccess = new UserAccess;
                $userAccess->object_type = 'project' ; 
                $userAccess->object_id = $project; 
                $userAccess->user_id = $userId; 
                $userAccess->access_type = $access; 
                $userAccess->save();
            }
            
        }
        
        $data =[];
        $data['name'] = $name;
        $data['email'] = $user->email;
        $data['password'] = $password;

 
        Mail::send('admin.registermail', ['user'=>$data], function($message)use($data)
        {  
            $message->to($data['email'], $data['name'])->subject('Welcome to Mylan!');
        });
        
         
        return redirect(url($hospitalSlug . '/users/' . $userId . '/edit'));
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
    public function edit($hospitalSlug,$id)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $projects = Projects:: all()->toArray(); 
        $user = User::find($id)->toArray();
        $userAccess = UserAccess::where(['user_id'=>$id,'object_type'=>'project'])->get()->toArray();  
        
         
        return view('hospital.user-edit')->with('active_menu', 'users')
                                          ->with('hospital', $hospital)
                                          ->with('logoUrl', $logoUrl)
                                          ->with('projects', $projects)
                                          ->with('userAccess', $userAccess)
                                          ->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $hospitalSlug, $userId)
    {
        $user = User::find($userId);
        $name =  ucfirst($request->input('name'));
        $user->name = $name;
 
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');     
        $user->has_all_access = ($request->has('has_all_access'))?'yes':'no';
        $user->save(); 

        $projects = $request->input('projects');
        if(!empty($projects))
        {
            foreach ($projects as $key => $project) {

                if($project=='')
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
                    $userAccess->object_type = 'project' ; 
                    $userAccess->object_id = $project; 
                    $userAccess->user_id = $userId; 
                    $userAccess->access_type = $access; 
                    $userAccess->save();
                }
                
            }
            
        }
        
        
        return redirect(url($hospitalSlug . '/users/' . $userId . '/edit'));
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
