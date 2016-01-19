@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


      <div class="pull-right">
                        <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Patient</a>
                    </div>
                  <div class="page-title">

                     <h3><span class="semi-bold">Dashboard</span></h3>
                 </div>
                  
                     <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                        <!-- <div class="row">
                           <div class="col-sm-6"><h3 class="margin-none"><span class="bold">Analytics</span></h3></div>
                            <div class="col-sm-3">
                              <select>
                                  <option value="10">Last 10 days</option>
                                  <option value="20">Last 20 days</option>
                                  <option value="30">Last 30 days</option>
                                  <option value="40">Last 40 days</option>
                              </select>
                            </div>
                            <div class="col-sm-3">
                              <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                            </div>
                        </div> -->
                        <form method="GET"> 
                     <div class="row">
 
                     <div class="col-sm-4">
                     <h3 class="margin-none"><span class="bold">Analytics</span></h3>
                     </div>
                     <div class="col-sm-3"> </div>
                     <div class="col-sm-5">
                    
                     <div class="row">
                     <div class="col-sm-9">
                         <div class="input-group input-daterange">
                             <input type="text" class="form-control" name="startDate" value="{{ $startDate }}">
                             <span class="input-group-addon">to</span>
                             <input type="text" class="form-control" name="endDate" value="{{ $endDate }}">
                         </div>
                         </div>
                         <div class="col-sm-3">
                         <button class="btn btn-default">Submit</button>
                         </div>
                         </div>
                     </div>
                 </div>
                 </form>

                     <hr class="margin-none">
                     <br>
                            <div class="row top-data">
                           <div class="col-md-2  ">
                              <h1 class="bold num-data">{{ count($responseCount['redFlags']['previousFlag'])}}  <span>/{{ count($responseCount['redFlags']['baseLineFlag'])}}</span>
                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="comparison with Previous /comparison with Base line "></i>
                               </h1>
                              <h5>Total Red <i class="fa fa-flag text-error" ></i> </h5>
                              <em class="line"></em>
                           </div>
                           <div class="col-md-2 ">
                                <h1 class="bold num-data">{{ count($responseCount['amberFlags']['previousFlag'])}}  <span>/ {{ count($responseCount['amberFlags']['baseLineFlag'])}}</span>
                                <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="comparison with Previous /comparison with Base line " ></i>
                               </h1>
                              <h5>Total Amber  <i class="fa fa-flag text-warning"></i> </h5>
                              <em class="line"></em>
                           </div>
                           <div class="col-md-2 ">
                                <h1 class="bold num-data"> {{ $responseCount['openSubmissions'] }}            
                                 
                               </h1>
                              <h5>No of Reviews</h5>
                              <em class="line"></em>
                           </div>
                           <div class="col-md-2">
                               <h1 class="bold num-data">{{ $responseCount['totalSubmissions'] }}
                                
                               </h1>
                              <h5>No of Total Submissions </h5>
                              <em class="line"></em>
                           </div>
                            <div class="col-md-2">
                               <h1 class="bold num-data">{{ $responseCount['missed'] }}
                                  
                               </h1>
                              <h5>No of Missed Submissions</h5>
                              <em class="line"></em>
                           </div>
                        </div>
                        <br>
                        <!-- Chart - Added -->
                        <br>
                        <div class="row">
                           <div class="col-sm-6"></div>
                           <div class="col-sm-3">
                           </div>
                           <div class="col-sm-3">
                                       <select name="generateChart">
                                      <option value="red_flags">No of red flags</option>
                                      <option value="amber_flags">No of amber flags</option>
                                      <option value="total_submissions">No of submissions</option>
                                      <option value="total_missed">No of missed</option>
                                      <option value="total_open_flags">No of open flags</option>
                                  </select>
                           </div>
                        </div>
                             <div id="chartdiv"></div>  
                        <!-- Chart - Added -->
                        <br>
                        <div class="row">
                           <div class="col-sm-5">
                               <div class="alert alert-info alert-black">
                                 Flags Summary
                              </div>
                     <table class="table table-flip-scroll dashboard-tbl">
                              <thead class="cf">
                                 <tr>
                                    <th width="25%" class="sorting">Patient ID<br>&nbsp;</th>
                                    <th class="sorting">Red <i class="fa fa-flag text-error"></i>
                                     <br> <sm>Prev <i class="iconset top-down-arrow"></i></sm>
                                      <sm>Base <i class="iconset top-down-arrow"></i></sm>
                                    </th class="sorting">
                                    <th class="sorting">Amber <i class="fa fa-flag text-warning"></i>
                                    <br> <sm>Prev <i class="iconset top-down-arrow"></i></sm>
                                      <sm>Base <i class="iconset top-down-arrow"></i></sm>
                                    </th>
                                    <th class="sorting">Green <i class="fa fa-flag text-success"></i>
                                     <br> <sm>Prev <i class="iconset top-down-arrow"></i></sm>
                                      <sm>Base <i class="iconset top-down-arrow"></i></sm>
                                    </th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php 
                                $i=1;
                              ?>
                              @foreach($patientsFlagSummary as $referenceCode => $patientFlagSummary)
                                <?php 
                                  if($i==6)
                                    break;
                                ?>
                                 <tr>
                                    <td class="  text-center">{{ $referenceCode }}</td>
                                    <td>
                                       <div class="  text-center">
                                          <h4 class="semi-bold margin-none flagcount">{{ count($patientFlagSummary['redPrevious']) }}<sm> / {{ count($patientFlagSummary['redBaseLine']) }}</sm></h4>
                                        
                                       </div>
                                    </td>
                                    <td>
                                       <div class="  text-center ">
                                             <h4 class="semi-bold margin-none flagcount">{{ count($patientFlagSummary['amberPrevious']) }}<sm> / {{ count($patientFlagSummary['amberBaseLine']) }}</sm></h4>
                                        
                                       </div>
                                    </td>
                                    <td> <div class=" text-center ">
                                             <h4 class="semi-bold margin-none flagcount">{{ count($patientFlagSummary['greenPrevious']) }}<sm> / {{ count($patientFlagSummary['greenBaseLine']) }}</sm></h4>
                                        
                                       </div>
                                    </td>
                                </tr>
                                <?php 
                                $i++;
                                ?>
                              
                                @endforeach
 
                              </tbody>
                     </table>
                     <hr style="    margin: 0px 0px 10px 0px;">
                       <div class="text-right">
                              <a href="flags.html" class="text-success">View All <i class="fa fa-long-arrow-right"></i></a>
                           </div>
                           </div>
                           <div class="col-sm-7">
                                <div class="alert alert-info alert-black">
                                 Submission Summary
                              </div>
                                <table class="table table-flip-scroll dashboard-tbl">
                              <thead class="cf">
                                 <tr> 
                                    <th class="sorting" width="16%">Patient ID</th>
                                    <th class="sorting">Submission#</th>
                                    <th class="sorting" width="22%">Total Score</th>
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
                                    
                                 </tr>
                              </thead>
                              <tbody>
                              <?php 
                                $i=1;
                              ?>
                              @foreach($submissionsSummary as $responseId=>$responseData)
                                <?php 
                                  if($i==6)
                                    break;
                                ?>
                                 <tr>
                                    <td class="text-center">{{ $responseData['patient'] }}</td>
                                    <td class="text-center">
                                      
                                        <h4 class="semi-bold margin-none flagcount">{{ $responseData['sequenceNumber'] }} on</h4>
                                        <sm>{{ $responseData['occurrenceDate'] }}</sm>
                                    
                                    </td>
                                     <td class="text-center">
                                     <h3 class="bold margin-none pull-left p-l-10">{{ $responseData['totalScore'] }}</h3>
                                     <sm class="text-muted sm-font">Prev - {{ $responseData['previousScore'] }} <i class="fa fa-flag "></i> </sm><br>
                                      <sm class="text-muted sm-font">Base - {{ $responseData['baseLineScore'] }} <i class="fa fa-flag "></i> </sm>
                                    </td>  
                                     
                                     <td class="text-center sorting">
                                     <span class="badge badge-important">{{ count($responseData['previousFlag']['red']) }}</span>
                                      <span class="badge badge-warning">{{ count($responseData['previousFlag']['amber']) }}</span>
                                     <span class="badge badge-success">{{ count($responseData['previousFlag']['green']) }}</span>
                                    </td>   
                                         <td class="text-center sorting">
                                     <span class="badge badge-important">{{ count($responseData['baseLineFlag']['red']) }}</span>
                                      <span class="badge badge-warning">{{ count($responseData['baseLineFlag']['amber']) }}</span>
                                     <span class="badge badge-success">{{ count($responseData['baseLineFlag']['green']) }}</span>
                                    </td>  

                                </tr>
                                <?php 
                                $i++;
                                ?>
                            @endforeach
                                  
                              </tbody>
                     </table>
                     <hr style="    margin: 0px 0px 10px 0px;">
                       <div class="text-right">
                              <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/submissions/' ) }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i></a>
                           </div>
                           </div>
                        </div>
                        <br><br>
                        <div class="row">
                        <div class="col-sm-offset-9 col-sm-3">
                        <div class="dataTables_filter" id="example_filter"><label>Search <input type="text" aria-controls="example" class="input-medium"></label></div>
                        </div>
                        </div>
                          <div class="alert alert-info alert-black">
                              Patients Summary
                          </div>
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
                                    <!-- <th class="sorting">Graph <br> <br></th> -->
                                 </tr>
                              </thead>
                              <tbody>
                               @foreach($allPatients as $patient)
                                <?php
                                  $patientId = $patient['id'];
                                  $patientStatus = $patient['account_status'];
                                  $referenceCode = $patient['reference_code'];
                                  

                                  if(!isset($patientsSummary[$referenceCode])) //inactive patient data
                                  {
                                      $patientsSummary[$referenceCode]['lastSubmission'] = '-';
                                      $patientsSummary[$referenceCode]['nextSubmission'] = '-';
                                      $patientsSummary[$referenceCode]['completed'] = [];
                                      $patientsSummary[$referenceCode]['missed'] = [];
                                      $patientsSummary[$referenceCode]['count'] = [];
                                      $patientsSummary[$referenceCode]['totalFlags'] = [];
                                  }
                                  
                                  $patientSummary = $patientsSummary[$referenceCode];
                                ?>
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}.'/'.$project['project_slug'].'/patients/{{ $patientId }}';">
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
                                 <!--    <td>
<div class="chart-block" style="padding:28px">
  120 <div id="line1" style="vertical-align: middle; display: inline-block; width: 100px; height: 30px;"></div> 6% 
</div> 
                                    </td>-->
                                 </tr>
                                  @endforeach  
                              </tbody>
                           </table>
                           <div class="text-right">
                              <a href="patients.html" class="text-success">View All <i class="fa fa-long-arrow-right"></i></a>
                           </div>
                      
                         
                      </div>
                     </div>
                  
        <style type="text/css">
        #chartdiv {
            width : 106%;
            height   : 300px;
         }                                                  
      .demo { position: relative; }
      .demo i {
        position: absolute; bottom: 10px; right: 24px; top: auto; cursor: pointer;
      }
      </style>

<script type="text/javascript">

  $(document).ready(function() {
     $('.input-daterange input').datepicker({
         format: 'dd-mm-yyyy'
     }); 

    var flagArr = {base_line: "Base Line", previous: "Previous"};

    projectDashbordChart(<?php echo $projectFlagsCount["redFlags"]; ?>,flagArr);

    $('select[name="generateChart"]').change(function (event) { 
      if($(this).val()=='red_flags')
      { 
        var flagArr = {base_line: "Base Line", previous: "Previous"};
        projectDashbordChart(<?php echo $projectFlagsCount['redFlags']; ?>,flagArr);
      }
      else if($(this).val()=='amber_flags')
      {
        var flagArr = {base_line: "Base Line", previous: "Previous"};
        projectDashbordChart(<?php echo $projectFlagsCount["amberFlags"]; ?>,flagArr);

      }
      else if($(this).val()=='total_submissions')
      {
        var flagArr = { completed: "Completed"} ; 
        projectDashbordChart(<?php echo $responseCount['completedSubmissionData']; ?>,flagArr);
      }
      else if($(this).val()=='total_missed')
      {
        var flagArr = { missed: "Missed"} ; 
        projectDashbordChart(<?php echo $responseCount['missedSubmissionData']; ?>,flagArr);
      }
      else if($(this).val()=='total_open_flags')
      {
        var flagArr =  { open_review: "Open Review"} ; 
        projectDashbordChart(<?php echo $responseCount['openSubmissionData']; ?>,flagArr);
      }

    });

    


  });


      </script>
@endsection