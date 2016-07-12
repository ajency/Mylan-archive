<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Hospital;
use App\User;
use App\Projects;
use \File;
use \Input;
use \Session;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hospitals = Hospital::orderBy('created_at')->get();
         
        return view('admin.hospital-list')->with('active_menu', 'hospital')
                                          ->with('hospitals', $hospitals);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         
        return view('admin.hospital-add')->with('active_menu', 'hospital');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		
		$urlSlug = str_slug($request->input('name').' '.$request->input('city'),'-');
		$validateurlSlug = Hospital::where('url_slug',$urlSlug)->get()->toArray();
        if(!empty($validateurlSlug))
        {
           Session::flash('error_message','Error !!! Hospital Already Exist ');    
           return redirect(url('admin/hospitals/create'));
        }
        $hospital = new Hospital;
        $name =  ucfirst($request->input('name'));
        $hospital->name = $name;
        $hospital->logo = $request->input('hospital_logo');
        $hospital->email = $request->input('email');
        $hospital->phone = $request->input('phone');  
        $hospital->address_line_1 = $request->input('address_line_1');
        $hospital->address_line_2 = $request->input('address_line_2');
        $hospital->city = $request->input('city');
        $hospital->country = $request->input('country');
        $hospital->postal_code = $request->input('postal_code');
        $hospital->website = $request->input('website');
        $hospital->primary_phone = $request->input('primary_phone');
        $hospital->primary_email = $request->input('primary_email');
        $hospital->contact_person_name = $request->input('contact_person');
        $hospital->url_slug = $urlSlug;
     
         
        $hospital->save();
        $hospitalId = $hospital->id;

        Session::flash('success_message','Hospital created successfully.');
         
        // return redirect(url('/admin/hospitals/' . $hospitalId . '/edit'));
        return redirect(url('/admin/hospitals'));
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
        $hospital = Hospital::find($id)->toArray(); 
        $imagePath = ($hospital['logo']!='')? url() . "/mylan/hospitals/".$hospital['logo']:'';
         
        return view('admin.hospital-edit')->with('active_menu', 'hospital')
                                          ->with('hospital', $hospital)
                                          ->with('imagePath', $imagePath);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $hospitalId)
    {	
		/*$urlSlug = str_slug($request->input('name').' '.$request->input('city'),'-');
		$validateurlSlug = Hospital::where('url_slug',$urlSlug)->where('id','!=',$hospitalId)->get()->toArray();
        if(!empty($validateurlSlug))
        {
           Session::flash('error_message','Error !!! Hospital Already Exist ');    
           return redirect(url('/admin/hospitals/' . $hospitalId . '/edit'));
        }*/
        $hospital = Hospital::find($hospitalId);
        $name =  ucfirst($request->input('name'));
        $hospital->name = $name;
        $hospital->email = $request->input('email');
        $hospital->phone = $request->input('phone');
        $hospital->address_line_1 = $request->input('address_line_1');
        $hospital->address_line_2 = $request->input('address_line_2');
        $hospital->city = $request->input('city');
        $hospital->country = $request->input('country');
        $hospital->postal_code = $request->input('postal_code');
        $hospital->website = $request->input('website');
        $hospital->primary_phone = $request->input('primary_phone');
        $hospital->primary_email = $request->input('primary_email');
        $hospital->contact_person_name = $request->input('contact_person');
        //$hospital->url_slug = $urlSlug;
         
        $hospital->save();
 
        Session::flash('success_message','Hospital details successfully updated.');

        return redirect(url('/admin/hospitals/' . $hospitalId . '/edit'));
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

    public function uploadLogo(Request $request,$hospitalId)
    {
        $targetDir = public_path() . "/mylan/hospitals/";
        $imageUrl = url() . "/mylan/hospitals/";

        File::makeDirectory( $targetDir, $mode = 0755, true, true );

        if ($request->hasFile( 'file' )) {
            
            $file = $request->file( 'file' );
     
            $fileName = $file->getClientOriginalName();
            $fileData = explode('.', $fileName);

            //$newFilename = rand() . '_' . $projectId . '.' . $fileExt;
            $newFilename = $fileName;

            $request->file( 'file' )->move( $targetDir, $newFilename );

            if($hospitalId)
            {
                $hospital = Hospital::find($hospitalId);
                $hospital->logo = $newFilename;
                $hospital->save();
            }
        
         }    

        return response()->json( [
                    'code' => 'logo_uploaded',
                    'message' => 'Image Uploaded' ,
                    'data' => [
                        'image_path' => $imageUrl . $newFilename,
                        'filename' => $newFilename
                    ]
            ], 201 );
    }

    public function deleteLogo(Request $request,$hospitalId)
    {

            $targetDir = public_path() . "/mylan/hospitals/";

            if($hospitalId)
            {
                $hospital = Hospital::find($hospitalId);
                $imageName= $hospital->logo;
                $hospital->logo = '';
                $hospital->save();
            }
            else
            {
                $imageName=$request['imageName'];
            }

            File::delete($targetDir.$imageName);
            

        return response()->json( [
                    'code' => 'logo_deleted',
                    'message' => 'Image deleted' ,
                
            ], 203 );
    }

    public function getHospitalPatients($hospitalId)
    {		
        $hospital = Hospital::find($hospitalId)->toArray(); 
        $patients = User::where('type',"patient")->where('hospital_id',$hospitalId)->orderBy('project_id')->get()->toArray();
        $patientsData = [];
        $project = [];
        foreach ($patients as  $patient) {
            $referenceCode = $patient['reference_code'];
            $projectId = $patient['project_id'];
            $date = date('d-m-y H:i:s', strtotime($patient['created_at']));

            if(!isset($project[$projectId]))
            {
                $project[$projectId] = Projects::find($projectId)->name;
                
            }
            
            $projectName = $project[$projectId];

            $referenceCode = $patient['reference_code'];
            $patientsData[] = ['referenceCode' =>$referenceCode, 'date' =>$date, 'projectName' =>$projectName];
        }
        
        return view('admin.hospital-patients')->with('active_menu', 'hospital')
                                          ->with('hospital', $hospital)
                                          ->with('patientsData', $patientsData)
                                          ->with('UserIdentity', \Auth::user()->type );

    }
}
