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

<div class="col-sm-8">
   <h1>Dashboard</h1>
</div>
<div class="col-sm-4">
   <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-primary pull-right m-t-10"><i class="fa fa-plus"></i> Add Patient</a>
</div>
<div class="grid simple ">
   <div class="grid-body no-border table-data grid-data-table">
      <div class="row">
         <div class="col-sm-8">
          <h3 class="">Analytics</h3>
       </div>
       <div class="col-sm-4 m-t-10">
         <form name="searchData" method="GET"> 
         <input type="hidden" class="form-control" name="startDate"  >
         <input type="hidden" class="form-control" name="endDate"  >
            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
               <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
               <span></span> <b class="caret"></b>
            </div>

         </form>
         <input type="hidden" name="flag" value="0">
          </div>
      </div>
   </div>
</div>
<div class="row top-data">
   <div class="col-md-2  ">
      <a href="red.html">
         <div class="tiles white added-margin">
            <div class="tiles-body">
               <h5 class="bold margin-none"> Red <i class="fa fa-flag text-error" ></i>&nbsp;  <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Total number of Red Flags generated across submissions"></i></h5>
               <p>Previous/Baseline </p>
               <h1 class="bold num-data">{{ count($responseCount['redFlags']['previousFlag'])}}<span>/{{ count($responseCount['redFlags']['baseLineFlag'])}}</span>
               </h1>
            </div>
         </div>
      </a>
   </div>
   <div class="col-md-2 ">
      <a href="amber.html">
         <div class="tiles white added-margin">
            <div class="tiles-body">
               <h5 class="bold margin-none"> Amber <i class="fa fa-flag text-warning" ></i>&nbsp;  <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Total number of Amber Flags generated across submissions"></i></h5>
               <p>Previous/Baseline </p>
               <h1 class="bold num-data">
               {{ count($responseCount['amberFlags']['previousFlag'])}}<span>/{{ count($responseCount['amberFlags']['baseLineFlag'])}}</span>
            </div>
         </div>
      </a>
   </div>
   <div class="col-md-2 ">
      <a href="submissions.html">
         <div class="tiles white added-margin">
            <div class="tiles-body">
               <h5 class="bold margin-none m-b-40"> Pending Reviews <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Submissions that have not been reviewed yet"></i></h5>
               <!-- <p>Lorem ipsum dolor </p> -->
               <h1 class="semi-bold num-data">
               {{ $responseCount['openSubmissions'] }}   
            </div>
         </div>
      </a>
   </div>
   <div class="col-md-6">
      <div class="tiles white added-margin">
         <div class="tiles-body">
            <div class="row">
               <div class="col-sm-8">
                  <div id="piechart"></div>
               </div>
               <div class="col-sm-4">
                  <h4 class="bold margin-none">{{ $responseCount['completed'] }}</h4>
                  <p> # Completed</p>
                  <h4 class="bold margin-none">{{ $responseCount['missed'] }}</h4>
                  <p> # Missed</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<br>
<div class="row">
   <div class="col-sm-4">
      <a href="patients.html">
         <div class="tiles white added-margin " style="zoom: 1;">
            <div class="tiles-body">
               <div class="tiles-title"> Total Patients </div>
               <div class="heading"> <span class="text-info animate-number" data-value="{{ $patients['patientsCount'] }}" data-animation-duration="1200">{{ $patients['patientsCount'] }}</span></div>
               <div class="progress transparent progress-small no-radius">
                  <div class="progress-bar progress-bar-green animate-progress-bar" data-percentage="26.8%" style="width: 26.8%;"></div>
               </div>
               <h5 class="text-black"><b><i class="fa fa-group"></i> &nbsp;{{ $patients['newPatients'] }}</b> New Patients Added </h5>
            </div>
         </div>
      </a>
      <br><br>
      <div class="tiles white added-margin hidden" style="zoom: 1;" >
         <div class="tiles-body">
            <!-- <div class="tiles-title"> Response Rate </div>
               <div class="heading text-error "> <span class=" animate-number" data-value="26.8" data-animation-duration="1200">26.8</span>% </div>
               <div class="progress transparent progress-small no-radius">
               <div class="progress-bar progress-bar-error animate-progress-bar" data-percentage="26.8%" style="width: 26.8%;"></div>
               </div>
               <h5 class="text-black"><b>10</b> Total Submitted / <b>07</b> Missed</h5>
               -->
         </div>
      </div>
   </div>
   <div class="col-sm-8">
      <div class="grid simple ">
         <div class="grid-body no-border table-data ">
            <div class="row">
               <div class="col-sm-6"></div>
               <div class="col-sm-6">
                  <select class="m-t-20 pull-right" name="generateChart">
                     <option value="red_flags">Red Flags</option>
                     <option value="amber_flags">Amber Flags</option>
                     <option value="total_open_flags">Open Submissions</option>
                     <option value="total_submissions">Completed Submissions</option>
                     <option value="total_missed">Missed Submissions</option>
                  </select>
     
               </div>
            </div>
            <div id="chartdiv" style="width : 112%; height   : 250px;"></div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-sm-6">
      <div class="grid simple grid-table">
         <div class="grid-title no-border">
            <h4>Flag <span class="semi-bold">Summary</span> <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Total flags across submissions for a patient"></i></h4>
            <div class="tools">
               <div class="dataTables_filter pull-right filter2" id="example_filter"><input type="text" aria-controls="example" class="input-medium" placeholder="search by patient id"></div>
            </div>
         </div>
         <div class="grid-body no-border" style="display: block;">
            <table class="table table-flip-scroll dashboard-tbl table-hover">
               <thead class="cf">
                  <tr>
                     <th width="25%" class="sorting">Patient ID<br>&nbsp;</th>
                     <th class="sorting">
                        Red <i class="fa fa-flag text-error"></i>
                        <br> 
                        <sm>Prev <i class="iconset top-down-arrow"></i></sm>
                        <sm>Base <i class="iconset top-down-arrow"></i></sm>
                        </th class="sorting">
                     <th class="sorting">
                        Amber <i class="fa fa-flag text-warning"></i>
                        <br> 
                        <sm>Prev <i class="iconset top-down-arrow"></i></sm>
                        <sm>Base <i class="iconset top-down-arrow"></i></sm>
                     </th>
                     <th class="sorting">
                        Green <i class="fa fa-flag text-success"></i>
                        <br> 
                        <sm>Prev <i class="iconset top-down-arrow"></i></sm>
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
                           <h4 class="semi-bold margin-none flagcount">
                              {{ count($patientFlagSummary['redPrevious']) }}
                              <sm> / {{ count($patientFlagSummary['redBaseLine']) }}</sm>
                           </h4>
                        </div>
                     </td>
                     <td>
                        <div class="  text-center ">
                           <h4 class="semi-bold margin-none flagcount">
                              {{ count($patientFlagSummary['amberPrevious']) }}
                              <sm> / {{ count($patientFlagSummary['amberBaseLine']) }}</sm>
                           </h4>
                        </div>
                     </td>
                     <td>
                        <div class=" text-center ">
                           <h4 class="semi-bold margin-none flagcount">
                              {{ count($patientFlagSummary['greenPrevious']) }}
                              <sm> / {{ count($patientFlagSummary['greenBaseLine']) }}</sm>
                           </h4>
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
               <a href="flags.html" class="text-success">View All <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;</a>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-6">
      <div class="grid simple grid-table">
         <div class="grid-title no-border">
            <h4>Submissions <span class="semi-bold">Summary</span> <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="List of individual submissions. Default sorting by time."></i></h4>
            <div class="tools">
               <div class="dataTables_filter pull-right filter2" id="example_filter"><input type="text" aria-controls="example" class="input-medium" placeholder="search by patient id"></div>
            </div>
         </div>
         <div class="grid-body no-border" style="display: block;">
            <table class="table table-flip-scroll table-hover dashboard-tbl">
               <thead class="cf">
                  <tr>
                     <th class="sorting" width="16%">Patient ID <br><br></th>
                     <th class="sorting"># Submission <i class="fa fa-angle-down" style="cursor:pointer;"></i><br><br></th>
                     <th class="sorting">Total Score <br><br></th>
                     <th class="sorting">
                        Previous
                        <br> 
                        <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                     </th>
                     <th class="sorting">
                        Baseline
                        <br> 
                        <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
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
                  <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
                     <td class="text-center">{{ $responseData['patient'] }}</td>
                     <td class="text-center">
                        <h4 class="semi-bold margin-none flagcount">{{ $responseData['occurrenceDate'] }}</h4>
                        <sm>Seq - {{ $responseData['sequenceNumber'] }}</sm>
                     </td>
                     <td class="text-center">
                        <h3 class="bold margin-none pull-left p-l-10">{{ $responseData['totalScore'] }}</h3>
                        <sm class="text-muted sm-font m-t-10">Prev - {{ $responseData['previousScore'] }}  <i class="fa fa-flag "></i> </sm>
                        <br>
                        <sm class="text-muted sm-font">Base - {{ $responseData['baseLineScore'] }} <i class="fa fa-flag "></i> </sm>
                     </td>
                     <td class="text-center sorting">
                        <span class="text-error">{{ count($responseData['previousFlag']['red']) }}</span>
                        <span class="text-warning">{{ count($responseData['previousFlag']['amber']) }}</span>
                        <span class=" text-success">{{ count($responseData['previousFlag']['green']) }}</span>
                     </td>
                     <td class="text-center sorting">
                        <span class="text-error">{{ count($responseData['baseLineFlag']['red']) }}</span>
                        <span class="text-warning">{{ count($responseData['baseLineFlag']['amber']) }}</span>
                        <span class=" text-success">{{ count($responseData['baseLineFlag']['green']) }}</span>
                     </td>
                  </tr>
                  <?php 
                    $i++;
                    ?>
                @endforeach
               </tbody>
            </table>
            <hr style="margin: 0px 0px 10px 0px;">
            <div class="text-right">
               <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/submissions/' ) }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-sm-12">
      <div class="grid simple grid-table grid-table-sort">
         <div class="grid-title no-border">
            <h4>Patient <span class="semi-bold">Summary</span></h4>
            <div class="tools"> <a href="javascript:;" class="collapse"></a>  </div>
         </div>
         <div class="grid-body no-border" style="display: block;">
            <table class="table table-flip-scroll table-hover">
               <thead class="cf">
                  <tr>
                     <th width="12%">Patient ID</th>
                     <th width="35%">Total Submissions</th>
                     <th class="sorting">
                        Compared To Previous
                        <br> 
                        <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                     </th>
                     <th class="sorting">
                        Compared To Baseline
                        <br> 
                        <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                     </th>
                     <th class="sorting">Graph <br> <br></th>
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
                  <tr onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                     <td>{{ $referenceCode }}</td>
                     <td>
                        <div class="lst-sub">
                           <h2 class="bold pull-left">
                              {{ count($patientSummary['completed']) }}<br>
                              <sm class="text-success">Completed</sm>
                           </h2>
                           <h2 class="bold pull-left">
                              {{ count($patientSummary['missed']) }}<br>
                              <sm class="text-danger">Missed</sm>
                           </h2>
                           <div class="pull-left p-t-20">
                              <span class="sm-font">Last Submission  <b>{{ $patientSummary['lastSubmission'] }}</b></span><br>
                              <span class="sm-font">Next Submission  <b>{{ $patientSummary['nextSubmission'] }}</b></span>
                           </div>
                        </div>
                     </td>
                     <td class="text-center sorting">
                        <span class=" text-error">
                        @if(isset($patientSummary['previousFlag']['red']))
                        {{ count($patientSummary['previousFlag']['red']) }}
                        @else
                        0
                       @endif
                                        </span>
                        <span class="text-warning">
                          @if(isset($patientSummary['previousFlag']['amber']))
                            {{ count($patientSummary['previousFlag']['amber']) }}
                            @else
                            0
                            @endif
                        </span>
                        <span class="text-success">
                          @if(isset($patientSummary['previousFlag']['green']))
                            {{ count($patientSummary['previousFlag']['green']) }}
                            @else
                            0
                           @endif
                        </span>
                     </td>
                     <td class="text-center sorting">
                        <span class=" text-error">
                          @if(isset($patientSummary['baseLineFlag']['red']))
                            {{ count($patientSummary['baseLineFlag']['red']) }}
                            @else
                            0
                           @endif
                        </span>
                        <span class="text-warning">
                          @if(isset($patientSummary['baseLineFlag']['amber']))
                          {{ count($patientSummary['baseLineFlag']['amber']) }}
                          @else
                          0
                          @endif
                        </span>
                        <span class="text-success">
                          @if(isset($patientSummary['baseLineFlag']['green']))
                            {{ count($patientSummary['baseLineFlag']['green']) }}
                            @else
                            0
                           @endif
                        </span>
                     </td>
                     <td>
                        <div class="chart-block" style="padding:28px">
                           <div id="line1" style="vertical-align: middle; display: inline-block; width: 100px; height: 30px;"></div>
                        </div>
                     </td>
                  </tr>
                  @endforeach  
                   
               </tbody>
            </table>
            <hr style="margin: 0px 0px 10px 0px;">
            <div class="text-right">
               <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/patients/' ) }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
            </div>
         </div>
      </div>
   </div>
</div>

 
<script type="text/javascript">
  var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
  var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

  $(document).ready(function() {


    //flags count chart

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
 
    //pie chart 
    var chart = AmCharts.makeChart( "piechart", {
         "type": "pie",
         "theme": "light",
         "dataProvider": [ {
         "title": "Completed",
         "value": {{ $responseCount['completed'] }}
         }, {
         "title": "Missed",
         "value": {{ $responseCount['missed'] }},
         "fillColor": "#CC0000",
         "fillAlphas": 0.5,
         } ],
         "titleField": "title",
         "valueField": "value",
         "labelRadius": 5,
         
         "radius": "42%",
         "innerRadius": "60%",
         "labelText": "[[title]]",
         "export": {
         "enabled": true
         }
         } );



  });


      </script>

@endsection