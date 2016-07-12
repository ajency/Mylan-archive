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
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Hospitals</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
               <div class="row">
                  <div class="col-lg-8 col-md-7">
                     <div class="page-title">
                        <h3 class="m-b-0"><span class="semi-bold">Hospitals</span></h3>
                        <p>(Showing all Hospital under Mylan)</p>
                     </div>
                  </div>
                  <div class="col-lg-4 col-md-5 m-t-25 text-right">
                     <div class="row">
                        <div class="col-md-12">
                           <a href="{{ url( 'admin/hospitals/create' ) }}" class="btn btn-success"><i class="fa fa-plus"></i> Add New Hospital</a>
                        </div>
                        <!-- <div class="col-md-6">
                           <select name="role" id="role" class="select2 form-control"  >
                              <option value="1">Sort By</option>
                              <option value="2">Name</option>
                              <option value="2">Popularity</option>
                           </select>
                        </div> -->
                     </div>
                  </div>
                  </div>

                  <div class="grid simple">
                     <div class="grid-body no-border">
                        <br>
                        @foreach($hospitals as $hospital)
                        <div>
                           <div class="pull-right">
                              <a target="_blank" href="/{{ $hospital['url_slug'] }}/projects" ><button class="btn btn-default btn-small m-r-15 default-light-btn">Login as {{ $hospital['name'] }} &nbsp;&nbsp;&nbsp;<i class="fa fa-angle-right"></i></button></a>
                         <!--      <span class="text-danger"><i class="fa fa-flag"></i> 5 New</span><span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                              <span class="text-warning"><i class="fa fa-flag"></i> 5 New</span> -->

                           </div>
                           
                              <h3><span class="semi-bold">{{ $hospital['name'] }},</span> {{ $hospital['city'] }}
                              <small class="m-l-10"><a href="/admin/hospitals/{{ $hospital['id'] }}/edit" class="brand-text fosz13"><i class="fa fa-pencil"></i> edit</a></small></h3>
                           </a>
                        </div>
                        <br>
                        <div class="row feature-list fivefetures">
                           <div class="col-md-4 b-r">
                              <h3 class="pull-right circ-bord">{{ $hospital->projects()->count() }}</h3>
                              <h5 class="semi-bold black"><i class="fa fa-flag text-muted"></i> PROJECTS</h5>
                              <div>
                                <!--  <p>
                                    Displays total number of flagged patients
                                 </p> -->
                                 <a href="/{{ $hospital['url_slug'] }}/projects/" class="brand-link tdu fosz12" target="_blank">View Projects</a>  
                              </div>
                           </div>
                           <!-- <div class="col-md-3 b-r">
                              <h3 class="pull-right text-info">10</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-flag text-muted"></i> OPEN FLAGS</h5>
                              <div>
                                 <p>
                                    Displays total number of flagged patients
                                 </p>
                                 <a href="project-flags.html" class="text-info">View Open Flags</a>  
                              </div>
                           </div>
                           <div class="col-md-3 b-r">
                              <h3 class="pull-right text-info">50%</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-comment text-muted"></i> RESPONSE RATE</h5>
                              <div>
                                 <p>
                                    Displays percentages of patients who regurarly take the questionnaire with Mylan
                                 </p>
                                 <a href="response-rate.html" class="text-info">View Response Rate</a>
                              </div>
                           </div> -->
                           <div class="col-md-4 b-r">
                              <h3 class="pull-right circ-bord">{{ $hospital->users()->where('type','project_user')->count() }}</h3>
                              <h5 class="semi-bold black"><i class="fa fa-users text-muted"></i> Project USERS</h5>
                              <div>
                            <!--      <p>
                                    2 Read<br>3 Edit
                                 </p> -->
                                 <a href="/{{ $hospital['url_slug'] }}/users/" class="brand-link tdu fosz12">View Users</a>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <h3 class="pull-right circ-bord">{{ $hospital->users()->where('type','patient')->count() }}</h3>
                              <h5 class="semi-bold black"><i class="fa fa-wheelchair text-muted"></i> PATIENTS</h5>
                              <div>
                              <!--    <p>
                                    {{ $hospital->users()->where('type','patient')->where('account_status','created')->count() }} newly registered patients since last week
                                 </p> -->
                                 <a href="{{ url('/admin/hospital/'.$hospital['id'].'/patients/') }}" class="brand-link tdu fosz12">View Patients</a>
                              </div>
                           </div>
                        </div>
                        <br>
                        <hr>
                        @endforeach
                        
                     </div>
                  </div>
      
 

@endsection