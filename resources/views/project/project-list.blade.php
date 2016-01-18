@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Projects</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


    <div class="pull-right m-t-10">
                     <a href="{{ url( $hospital['url_slug'].'/projects/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Project</a>
                  </div>
                  <div class="page-title">
                     <h3 class="m-b-0"><span class="semi-bold">Projects</span></h3>
                     <p>(Showing all Projects under {{ $hospital['name'] }})</p>
                  </div>
                  <div class="grid simple">
                     <div class="grid-body no-border">
                        <br>
                        @foreach($projects as $project)
                        <div>
                           <div class="pull-right">
                              <a target="_blank" href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}" ><button class="btn btn-default btn-small m-r-15">Login as {{$project['name']}}</button></a>
                              <!-- <span class="text-danger"><i class="fa fa-flag"></i> 5 New</span><span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                              <span class="text-warning"><i class="fa fa-flag"></i> 5 New</span> -->
                           </div>
                           <a href="{{ url( $hospital['url_slug'].'/projects/'. $project['id'] .'/edit' ) }}">
                              <h3><span class="semi-bold">{{$project['name']}}</span></h3>
                           </a>
                        </div>
                        <br>
                        <em>{{$project['description']}}</em>
                        <!-- <br><br><br>
                        <div class="row feature-list">
                           <div class="col-md-3 b-r">
                              <h3 class="pull-right text-success">20</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-flag text-muted"></i> OPEN FLAGS</h5>
                              <div>
                                 <p>
                                    Displays total number of flagged patients
                                 </p>
                                 <a href="project-flags.html" class="text-success">View Open Flags</a>  
                              </div>
                           </div>
                           <div class="col-md-3 b-r">
                              <h3 class="pull-right text-success">80%</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-comment text-muted"></i> RESPONSE RATE</h5>
                              <div>
                                 <p>
                                    Displays percentages of patients who regurarly take the questionnaire with Mylan
                                 </p>
                                 <a href="response-rate.html" class="text-success">View Response Rate</a>
                              </div>
                           </div>
                           <div class="col-md-3 b-r">
                              <h3 class="pull-right text-success">35</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-users text-muted"></i>  USERS</h5>
                              <div>
                                 <p>
                                    2 Read<br>3 Edit
                                 </p>
                                 <a href="project-users.html" class="text-success">View Users</a>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <h3 class="pull-right text-success m-0">60</h3>
                              <h5 class="semi-bold black m-b-20"><i class="fa fa-wheelchair text-muted"></i> PATIENTS</h5>
                              <div>
                                 <p>
                                    3 newly registered patients since last week
                                 </p>
                                 <a href="project-patients.html" class="text-success">View Patients</a>
                              </div>
                           </div>
                        </div> -->
                        <br>
                        <hr>
                        @endforeach
                        
                     </div>
                  </div>

@endsection