<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Hospital;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hospitals = Hospital::orderBy('created_at')->get()->toArray();
         
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

        $hospital = new Hospital;
        $name =  ucfirst($request->input('name'));
        $hospital->name = $name;
        $hospital->logo = $request->input('hospital_logo');
        $hospital->email = $request->input('email');
        $hospital->phone = $request->input('phone');
        $hospital->address = $request->input('address');
        $hospital->website = $request->input('website');
        $hospital->primary_phone = $request->input('primary_phone');
        $hospital->primary_email = $request->input('primary_email');
        $hospital->contact_person_name = $request->input('contact_person');
        // $hospital->location = $request->input('location');
         
        $hospital->save();
        $hospitalId = $hospital->id;
         
        return redirect(url('/admin/hospitals/' . $hospitalId . '/edit'));
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
        $hospital = Hospital::find($hospitalId);
        $name =  ucfirst($request->input('name'));
        $hospital->name = $name;
        $hospital->email = $request->input('email');
        $hospital->phone = $request->input('phone');
        $hospital->address = $request->input('address');
        $hospital->website = $request->input('website');
        $hospital->primary_phone = $request->input('primary_phone');
        $hospital->primary_email = $request->input('primary_email');
        $hospital->contact_person_name = $request->input('contact_person');
        // $hospital->location = $request->input('location');
         
        $hospital->save();
 
         
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
}
