@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Users</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="pull-right m-t-25">
                     <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a>
                     <a href="{{ url( $hospital['url_slug'].'/users/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</a>
                  </div>
<div class="page-title">
                     <h3><span class="semi-bold">Users</span></h3>
                      <p>(List User for {{ $hospital['name'] }})</p>
                  </div>
                  <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                           <table class="table table-hover" id="example" >
                              <thead>
                                 <tr>
                                    <th>Name</th>
                                    <th>Email ID</th>
                                    <th>Contact</th>
                                    <th>Access</th>
                                  </tr>
                              </thead>
                              <tbody>
                              @foreach($users as $user)
                                 <tr class="odd gradeX" onclick="window.document.location='{{ url( $hospital['url_slug'].'/users/'.$user['id'].'/edit' ) }}';">
                                    <td>{{ $user['name'] }}</td>
                                    <td>{{ $user['email'] }}</td>
                                    <td class="center">{{ $user['phone'] }}</td>
                                    <td class="center"><i class="fa fa-eye"></i>&nbsp;&nbsp;<a href="#"><i class="fa fa-pencil-square-o"></i></a></td>
                                 </tr>
                              @endforeach
                                 
                             </tbody>
                           </table>
                        </div>
                     </div>

@endsection