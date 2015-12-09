@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Hospitals</a>
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
                        <div class="col-lg-4 col-md-5 m-t-25">
                           <div class="row">
                              <div class="col-md-6">
                                 <a href="{{ url( 'admin/hospitals/create' ) }}" class="btn btn-success"><i class="fa fa-plus"></i> Add New Hospital</a>
                              </div>
                              <div class="col-md-6">
                                 <select name="role" id="role" class="select2 form-control"  >
                                    <option value="1">Sort By</option>
                                    <option value="2">Name</option>
                                    <option value="2">Popularity</option>
                                 </select>
                              </div>
                           </div>
                        </div>

                  </div>

                  <div class="grid simple">
                     <div class="grid-body no-border">
                        <br>
                        @foreach($hospitals as $hospital)
                        <div>
                           <div class="pull-right">
                              <button class="btn btn-default btn-small m-r-15">Login as {{ $hospital['name'] }}</button>
                              <span class="text-danger"><i class="fa fa-flag"></i> 5 New</span><span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                              <span class="text-warning"><i class="fa fa-flag"></i> 5 New</span>

                           </div>
                           <a href="single-hospital.html">
                              <h3><span class="semi-bold">{{ $hospital['name'] }},</span> {{ $hospital['location'] }}</h3>
                           </a>
                        </div>
                        <br>
                        <div class="row feature-list fivefetures">
                           <div class="col-md-3 b-r">
                              <h3 class="pull-right text-info">10</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-flag text-muted"></i> PROJECTS</h5>
                              <div>
                                 <p>
                                    Displays total number of flagged patients
                                 </p>
                                 <a href="project-flags.html" class="text-info">View Projects</a>  
                              </div>
                           </div>
                           <div class="col-md-3 b-r">
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
                           </div>
                           <div class="col-md-3 b-r">
                              <h3 class="pull-right text-info">35</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-users text-muted"></i>  USERS</h5>
                              <div>
                                 <p>
                                    2 Read<br>3 Edit
                                 </p>
                                 <a href="project-users.html" class="text-info">View Users</a>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <h3 class="pull-right text-info m-0">60</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-wheelchair text-muted"></i> PATIENTS</h5>
                              <div>
                                 <p>
                                    3 newly registered patients since last week
                                 </p>
                                 <a href="project-patients.html" class="text-info">View Patients</a>
                              </div>
                           </div>
                        </div>
                        <br>
                        <hr>
                        @endforeach
                        
                     </div>
                  </div>
      
 

@endsection