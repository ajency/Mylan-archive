<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\UserAccess;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $userAccess = UserAccess::find($id)->delete();

        return response()->json([
                    'code' => 'useraccess_deleted',
                    'message' => 'User Hopital Access Successfully Deleted'
                        ], 204);
    }

     public function deleteProjectAccess($hospitalSlug,$id)
    {
        $userAccess = UserAccess::find($id)->delete();

        return response()->json([
                    'code' => 'useraccess_deleted',
                    'message' => 'User Project Access Successfully Deleted'
                        ], 204);
    }
}
