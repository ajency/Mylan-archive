@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active" > HOME</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Patients</a>
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
                       <a href="{{ url($hospital['url_slug'].'/patients/create' ) }}" class="btn btn-success"><i class="fa fa-plus"></i> Add New Patient</a>
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
                  <table class="table table-flip-scroll ">
                              <thead class="cf">
                                 <tr>
                                    <th>Patient ID</th>
                                    <th>Total Submissions</th>
                                    <th>Base Line Green flags</th>
                                    <th>Previous Green flags</th>
                                    <th>Base Line Red Flags</th>
                                    <th>Previous Red Flags</th>
                                    <th>Base Line amber flags</th>
                                    <th>Previous amber flags</th>
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
                                      $patientsSummary[$referenceCode]['count'] = [];
                                      $patientsSummary[$referenceCode]['totalFlags'] = [];
                                  }
                                  
                                  $patientSummary = $patientsSummary[$referenceCode];
                                ?>
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/patients/{{ $patientId }}';">
                                    <td class="ttuc patient-refer{{ $referenceCode }}">{{ $referenceCode }}</td> 
                                    <td>
                                       <div class="lst-sub">
                                          <h2 class="bold pull-left">{{ count($patientSummary['count']) }}</h2>
                                          <div class="pull-left m-t-5">
                                             <span class="sm-font">Last Submission  <b>{{ $patientSummary['lastSubmission'] }}</b></span>
                                              <span class="sm-font">Next Submission  <b>{{ $patientSummary['nextSubmission'] }}</b></span>
                                               <span class="sm-font">Total Missed  <b>{{ count($patientSummary['missed']) }}</b></span>
                                          </div>

                                       </div>

                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold text-success">
                                           @if(isset($patientSummary['baseLineFlag']['green']))
                                            {{ count($patientSummary['baseLineFlag']['green']) }}
                                            @else
                                            0
                                           @endif
                                             </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold text-success">
                                            @if(isset($patientSummary['previousFlag']['green']))
                                            {{ count($patientSummary['previousFlag']['green']) }}
                                            @else
                                            0
                                           @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold text-error">
                                           @if(isset($patientSummary['baseLineFlag']['red']))
                                            {{ count($patientSummary['baseLineFlag']['red']) }}
                                            @else
                                            0
                                           @endif
                                             </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold text-error">
                                            @if(isset($patientSummary['previousFlag']['red']))
                                            {{ count($patientSummary['previousFlag']['red']) }}
                                            @else
                                            0
                                           @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                        <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold text-warning">
                                            @if(isset($patientSummary['baseLineFlag']['amber']))
                                            {{ count($patientSummary['baseLineFlag']['amber']) }}
                                            @else
                                            0
                                            @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                        <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold text-warning">
                                            @if(isset($patientSummary['previousFlag']['amber']))
                                            {{ count($patientSummary['previousFlag']['amber']) }}
                                            @else
                                            0
                                            @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
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
