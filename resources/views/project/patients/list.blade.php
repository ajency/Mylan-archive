@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
         <li>
            <a href="#"> Patients</a>
         </li>
          
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
                <div class="row">
                <div class="col-lg-8 col-md-7">
                  <div class="page-title">
                     <h3 class="m-b-0"><span class="semi-bold">Patients</span></h3>
                     <p>(Showing all Patients under {{ $hospital['name'] }})</p>
                  </div>
                  </div>
                    <div class="col-lg-4 col-md-5 m-t-25 text-right">
                       <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-success"><i class="fa fa-plus"></i> Add New Patient</a>
                    </div>
                </div>
                <div class="grid simple">
                                 <div class="grid-body no-border table-data">
                          <br>
                       <!--        <div class="row">
                                <div class="col-sm-6"> <h3 class="bold margin-none">Response Rate</h3></div>  
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-4 pull-right">
                                    <div class="input-group input-daterange">
                                        <input type="text" class="form-control" value="2012-04-05">
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="form-control" value="2012-04-19">
                                    </div>
                                </div>
                            </div>
                            <hr> -->
                            <div class="row ">
                                <div class="col-md-6 ">
                                    <div class="tiles white added-margin light-gray" style="zoom: 1;">
                                        <div class="tiles-body">
                                            <div class="tiles-title"> Response Rate </div>
                                            <div class="heading"> <span class="animate-number" data-value="{{ $responseRate }}" data-animation-duration="1200">{{ $responseRate }}</span>% </div>
                                            <div class="progress transparent progress-small no-radius">
                                                <div class="progress-bar progress-bar-black animate-progress-bar" data-percentage="{{ $responseRate }}%" style="width: {{ $responseRate }}%;"></div>
                                            </div>
                                            <h5 class="text-black"><b>{{ $completedResponses }}</b> Total Submitted / <b>{{ $missedResponses }}</b> Total Missed</span></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="tiles white added-margin light-gray" style="zoom: 1;">
                                        <div class="tiles-body">
                                            <div class="tiles-title"> Total Patients </div>
                                            <div class="heading"> <span class="animate-number" data-value="{{ count($patients) }}" data-animation-duration="1200">{{ count($patients) }}</div>
                              <div class="progress transparent progress-small no-radius">
                              <div class="progress-bar progress-bar-black animate-progress-bar" data-percentage="{{ count($patients) }}%" style="width: {{ count($patients) }}%;"></div>
                              </div>
                              <h5 class="text-black"><b><i class="fa fa-group"></i> &nbsp;{{ $newPatients }}</b> New Patients Added </h5>
                            
                              </div>
                              </div>
                           </div>
                         
                        </div>
                        <br>
                        <div class="alert alert-info alert-black">
                              Patients Summary
                          </div>
                  <div class="grid-body">
                 <table class="table table-flip-scroll table-hover">
                              <thead class="cf">
                                 <tr>
                                    <th>Patient ID</th>
                                    <th width="35%">Total Submissions</th>
                                     <th class="sorting">Compared To Previous
                                     <br> <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                                    </th>
                                    <th class="sorting">Compared To Baseline
                                    <br> <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                                    </th>
                                    <th class="sorting">Graph <br> <br></th>
                                 </tr>
                              </thead>
                              <tbody>
                               @foreach($patients as $patient)
                                <?php
                                  $patientId = $patient['id'];
                                  $patientStatus = $patient['account_status'];
                                  $referenceCode = $patient['reference_code'];
                                  

                                  if(!isset($patientsSummary[$referenceCode])) //inactive patient data
                                  {
                                      $patientsSummary[$referenceCode]['lastSubmission'] = '-';
                                      $patientsSummary[$referenceCode]['nextSubmission'] = '-';
                                      $patientsSummary[$referenceCode]['missed'] = [];
                                      $patientsSummary[$referenceCode]['completed'] = [];
                                      $patientsSummary[$referenceCode]['count'] = [];
                                      $patientsSummary[$referenceCode]['totalFlags'] = [];
                                  }
                                  
                                  $patientSummary = $patientsSummary[$referenceCode];
                                ?>
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/patients/{{ $patientId }}';">
                                    <td>{{ $referenceCode }}</td>
                                    <td>
                                       <div class="lst-sub">
                                          <h2 class="bold pull-left">{{ count($patientSummary['completed']) }}<br>
                                          <sm class="text-success">Total Done</sm>
                                          </h2>
                                           <h2 class="bold pull-left">{{ count($patientSummary['missed']) }}<br>
                                          <sm class="text-danger">Total Missed</sm>
                                          </h2>
                                          <div class="pull-left p-t-20">
                                             <span class="sm-font">Last Submission  <b>{{ $patientSummary['lastSubmission'] }}</b></span><br>
                                              <span class="sm-font">Next Submission  <b>{{ $patientSummary['nextSubmission'] }}</b></span>
                                              
                                          </div>

                                       </div>

                                    </td>
                                    <td class="text-center sorting">
                                     <span class="badge badge-important">
                                       @if(isset($patientSummary['previousFlag']['red']))
                                        {{ count($patientSummary['previousFlag']['red']) }}
                                        @else
                                        0
                                       @endif                                       
                                     </span>
                                      <span class="badge badge-warning">
                                        @if(isset($patientSummary['previousFlag']['amber']))
                                        {{ count($patientSummary['previousFlag']['amber']) }}
                                        @else
                                        0
                                        @endif
                                      </span>
                                     <span class="badge badge-success">
                                     @if(isset($patientSummary['previousFlag']['green']))
                                      {{ count($patientSummary['previousFlag']['green']) }}
                                      @else
                                      0
                                     @endif
                                           </span>
                                    </td> 
                                    <td class="text-center sorting">
                                     <span class="badge badge-important">
                                       @if(isset($patientSummary['baseLineFlag']['red']))
                                        {{ count($patientSummary['baseLineFlag']['red']) }}
                                        @else
                                        0
                                       @endif
                                     </span>
                                      <span class="badge badge-warning">
                                        @if(isset($patientSummary['baseLineFlag']['amber']))
                                        {{ count($patientSummary['baseLineFlag']['amber']) }}
                                        @else
                                        0
                                        @endif
                                      </span>
                                     <span class="badge badge-success">
                                      @if(isset($patientSummary['baseLineFlag']['green']))
                                      {{ count($patientSummary['baseLineFlag']['green']) }}
                                      @else
                                      0
                                     @endif
                                     </span>
                                    </td> 
                                    <td>
<!-- <div class="chart-block" style="padding:28px">
  120 <div id="line1" style="vertical-align: middle; display: inline-block; width: 100px; height: 30px;"></div> 6% 
</div> -->
                                    </td>
                                 </tr>
                                  @endforeach  
                              </tbody>
                           </table>
                  </div>
                  </div>
 
                  
      <script type="text/javascript">
      $(document).ready(function() {
         $('.input-daterange input').datepicker({
             format: 'dd-mm-yyyy'
         }); 
      }); 
      </script>      
 

@endsection
