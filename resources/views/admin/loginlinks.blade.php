@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Login Links</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

 
    <div class="pull-right m-t-10">
                     
                  </div>
                  <div class="page-title">
                     <h3 class="m-b-0"><span class="semi-bold">{{ ($accessData['type']=='Hospital')?$accessData['type']: ucfirst($accessData['links'][0]['HOSPITALNAME']) }} Project Links</span></h3>
                      
                  </div>
                  <div class="grid simple">
                     <div class="grid-body no-border">
                        <br>
                        @if(isset($accessData['links']))
                        @foreach($accessData['links'] as $data)


                        <!-- <div>
                           <div class="pull-right">
                              <a target="_blank" href="{{ $data['URL'] }}" ><button class="btn btn-default btn-small m-r-15">Login as {{ $data['loginName']}}</button></a>
                     
                           </div>
                            
                              <h3><span class="semi-bold">{{ $data['NAME'] }}</span></h3>
                            
                        </div>
                        <br>
                  
                        <hr> -->
                        @if($accessData['type'] == 'Hospital')
                        <!-- Hospital HTML -->
                           <div>
                           <div class="pull-right">
                              <a target="_blank" href="{{ $data['URL'] }}" ><button class="btn btn-default btn-small m-r-15 default-light-btn">Login as {{ $data['loginName']}} &nbsp;&nbsp;&nbsp;<i class="fa fa-angle-right"></i></button></a>
                         
                           </div>
                           
                              <h3><span class="semi-bold">{{ $data['NAME'] }},</span> {{ $data['CITY'] }}</h3>
                           
                        </div>
                        <br>
                        <div class="row feature-list fivefetures">
                           <div class="col-md-4 b-r">
                              <h3 class="pull-right circ-bord">{{ $data['PROJECTCOUNT'] }}</h3>
                              <h5 class="semi-bold black"><i class="fa fa-flag text-muted"></i> PROJECTS</h5>
                              <div>
                                 <a href="{{ $data['URL'] }}/projects/" class="brand-link tdu fosz12" target="_blank">View Projects</a>  
                              </div>
                           </div>
                          
                           <div class="col-md-4 b-r">
                              <h3 class="pull-right circ-bord">{{ $data['PROJECTUSERCOUNT'] }}</h3>
                              <h5 class="semi-bold black"><i class="fa fa-users text-muted"></i> Project USERS</h5>
                              <div>

                                 <a href="{{ $data['URL'] }}/users/" class="brand-link tdu fosz12" target="_blank">View Users</a>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <h3 class="pull-right circ-bord">{{ $data['PATIENTCOUNT'] }}</h3>
                              <h5 class="semi-bold black"><i class="fa fa-wheelchair text-muted"></i> PATIENTS</h5>
                              <div>
                                 <a href="{{ url('/admin/hospital/'.$data['ID'].'/patients/') }}" class="brand-link tdu fosz12">View Patients</a>
                              </div>
                           </div>
                        </div>
                        <br>
                        <hr>
                         <!-- /Hospital HTML -->
                        @else
                            <!-- Project HTML -->
                           <div>
                           <div class="pull-right">
                              <a target="_blank" href="{{ $data['URL'] }}?login=project" ><button style="background: #f2f4f5;" class="btn btn-default btn-small m-r-15">Login as {{ $data['loginName']}} &nbsp;&nbsp;&nbsp;<i class="fa fa-angle-right"></i></button></a>
                             
                           </div>
                           
                              <h3><span class="semi-bold">{{ $data['NAME'] }}</span> &nbsp;</h3>
                           
                        </div>
                         <em>{{ $data['DESCRIPTION'] }}</em>
                        
                        <br>
                        <hr>
                        <!-- /Project HTML -->
                        @endif



                        @endforeach
                        @else 
                         <div>
                   
                            
                              <h3><span class="semi-bold">No data found</span></h3>
                            
                        </div>
                        @endif
                        
                     </div>
                  </div>

@endsection