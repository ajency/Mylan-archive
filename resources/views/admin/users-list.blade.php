@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Hospital Users</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="pull-right m-t-25">
                     <!-- <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a> -->
                     <a href="{{ url( 'admin/users/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</a>
                  </div>
<div class="page-title">
                     <h3><span class="semi-bold">Hospital Users</span></h3>
                      <p>(Users on Mylan)</p>
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
                                 <tr class="odd gradeX" onclick="window.document.location='{{ url( 'admin/users/'.$user['id'].'/edit' ) }}';">
                                    <td>{{ $user['name'] }}</td>
                                    <td>{{ $user['email'] }}</td>
                                    <td class="center">{{ $user['phone'] }}</td>
                                    <td class="center">
                                    @if($user['view'])
                                       <i class="fa fa-eye"></i>&nbsp;&nbsp;
                                    @endif
                                    
                                    @if($user['edit'])
                                       <i class="fa fa-pencil-square-o"></i>
                                    @endif
                                      </td>
                                 </tr>
                              @endforeach
                                 
                             </tbody>
                           </table>
                        </div>
                     </div>

@endsection