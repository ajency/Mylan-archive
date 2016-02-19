@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <!-- <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
      </ul>
      </p> -->
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div class="col-sm-8">
                     <h1>Dashboard</h1>
                  </div>
                  <div class="col-sm-4">
                     <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create') }}" class="btn btn-primary pull-right m-t-10"><i class="fa fa-plus"></i> Add Patient</a>
                  </div>
                  <div class="grid simple ">
                     <div class="grid-body no-border table-data grid-data-table">
                        <div class="row">
                           <div class="col-sm-8">
                              <h3 class="">Analytics</h3>
                           </div>
                           <div class="col-sm-4 m-t-10">
                              <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
                                 <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                 <span></span> <b class="caret"></b>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
            <div class="col-sm-6 ">
<div class="tiles white">
   <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients?patients=created') }}">
                           <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                          <h4> <i class="fa fa-users"></i> Total Recruited Patients: <b class="bigger text-success pull-right">{{ $activepatients }} </b> </h4>
                         </div>
   </a>                      
                         </div>
                     </div>
                     <div class="col-sm-6">
                     <div class="tiles white">
         <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients?patients=active') }}">
                           <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                            <h4> <i class="fa fa-users"></i> Total Active Patients:  <b class="bigger text-success pull-right">{{ $allpatientscount }} </b></h4>
                           </div>
         </a>
                         </div>
                     </div>
                  </div>
      <div class="row">
         <div class="col-sm-8">

                  <div class="row top-data m-t-10">

                     <div class="col-md-2  ">
                        <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=red">
                           <div class="tiles white added-margin"  onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=red';">
                              <div class="tiles-body">
                                 <h5 class="bold m-0"> Red <i class="fa fa-flag text-error" ></i>&nbsp;  <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Total number of Red Flags generated across submissions"></i></h5>

                                 <p class="p-t-10 m-0 text-muted">Previous / Baseline </p>
                                 <h2 class="m-0"><a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=red&type=previous"><b class="grey">{{ $responseCount['redPrevious'] }} </b></a>/<a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=red&type=baseline"><b class="f-w grey"> {{ $responseCount['redBaseLine'] }}</b></a></h2>
                              </div>
                           </div>
                        </a>
                     </div>
                     <div class="col-md-2 ">
                        <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=amber">
                           <div class="tiles white added-margin">
                              <div class="tiles-body">
                                 <h5 class="bold m-0"> Amber <i class="fa fa-flag text-warning" ></i>&nbsp;  <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Total number of Amber Flags generated across submissions"></i></h5>


                                 <p class="p-t-10 m-0 text-muted">Previous / Baseline </p>
                                 <h2 class="m-0"><a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=amber&type=previous"><b class="grey">{{ $responseCount['amberPrevious'] }} </b></a>/<a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=amber&type=baseline"><b class="f-w grey"> {{ $responseCount['amberBaseLine'] }}</b></a></h2>

                              </div>
                           </div>
                        </a>
                     </div>
                     <div class="col-md-2 ">
                        <a href="#">
                           <div class="tiles white added-margin">
                              <div class="tiles-body p-17">
                                 <h5 class="bold m-b-20 m-t-0">Unreviewed Submissions <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Submissions that have not been reviewed yet"></i></h5>
                                 <!-- <p>Lorem ipsum dolor </p> -->
                                 <h2 class="bold">
                                  <b class="grey">{{ $responseCount['unreviewedSubmission'] }}</b></h2>
                              </div>
                           </div>
                        </a>
                     </div>
                 
                  </div>

                  <div class="row m-t-20">
                      <div class="col-md-12">
                        <div class="tiles white">
                           <div class="tiles-body">
                           <br>
                              <div class="row">
                                 <div class="col-sm-5">
                                    <div id="piechart"></div>
                                 </div>
                                 <div class="col-sm-7">
                                          <div class="col-sm-12 m-t-30">
                                             <div class="col-sm-4 text-center">
                                                <h2 class="bold m-0 inline">{{ $responseCount['completed'] }}%</h2>
                                                <p> # Completed</p>
                                             </div>
                                             <div class="col-sm-4 text-center">
                                                <h2 class="bold m-0 inline">{{ $responseCount['late'] }}%</h2>
                                                <p> # Late</p>
                                             </div>
                                             <div class="col-sm-4 text-center">
                                                <h2 class="bold m-0 inline">{{ $responseCount['missed'] }}%</h2>
                                                <p> # Missed</p>
                                             </div>
                                             
                                          </div>
                                       
                                 </div>
                              </div>
                              <br>
                           </div>
                        </div>
                     </div>
                  </div>

         </div>
         <div class="col-sm-4">
            <div class=" simple grid-table m-t-20">
                           <div class="grid-title no-border">
                              <h4>Alerts <span class="semi-bold">Notification</span> <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Activity"></i></h4>
                        
                           </div>
                           <div class="grid-body no-border" style="display: block;">
                           No New Notification
  <!--                           <div class="notification-messages info">
                             
      <div class="message-wrapper">
         <div class="heading"> New Patient ID Generated </div>
         <div class="description"> 1000001 </div>
      </div>
      <div class="date pull-right"> Just now </div>
      <div class="clearfix"></div>
   </div>
   <div class="notification-messages danger">
     
      <div class="message-wrapper">
         <div class="heading">Patient ID 1000001   </div>
         <div class="description"> Missed 1 Questionnaire </div>
      </div>
      <div class="date pull-right"> Yesterday </div>
      <div class="clearfix"></div>
   </div>
  <div class="notification-messages danger">
     
      <div class="message-wrapper">
         <div class="heading">Patient ID 1000003   </div>
         <div class="description"> Missed 1 Questionnaire </div>
      </div>
      <div class="date pull-right"> Yesterday </div>
      <div class="clearfix"></div>
   </div>

                              
                              <div class="text-right">
                                 <a href="notification.html" class="text-success">View All <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;</a>
                              </div> -->
                           </div>
                        </div>
         </div>
      </div>




                  <br>
                  <div class="grid simple ">
                           <div class="grid-body no-border table-data ">
                              <div class="row">
                                 <div class="col-sm-6"><h4 class="m-t-25">Health <span class="semi-bold">Tracker</span></h4></div>
                                 <div class="col-sm-6">
                                 <select class="m-t-20 pull-right" name="generateChart">
                                   
                                   <option value="red_flags" selected> Red Flags</option>
                                   <option value="amber_flags">  Amber Flags</option>
                                   <option value="unreviewed" >Unreviewed Submissions</option>
                                   <option value="submissions">Submissions</option>
                                </select>
                           
                                 </div>
                              </div>
                              <div id="chartdiv" style="width:100%;"></div>
                           </div>
                        </div>
                  <div class="grid simple grid-table">
                           <div class="grid-title no-border">

                              <h4>Submissions <span class="semi-bold">Summary</span> <!-- <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="List of individual submissions. Default sorting by time."></i> --><sm class="light">(These are scores & flags for current submissions)</sm></h4>

                              <!-- <div class="tools">
                                 <div class="dataTables_filter pull-right filter2" id="example_filter"><input type="text" aria-controls="example" class="input-medium" placeholder="search by patient id"></div>
                              </div> -->
                           </div>
                           <div class="grid-body no-border" style="display: block;">
                              <table class="table table-flip-scroll table-hover dashboard-tbl">
                          <thead class="cf">
                             <tr>
                                <th class="sorting" width="16%">Patient ID <br><br></th>
                                <th class="sorting"># Submission <!-- <i class="fa fa-angle-down" style="cursor:pointer;"></i> --><br><br></th>
                                <th colspan="3" class="sorting">
                                   Total Score
                                   <br> 
                                   <sm>Base <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                   <sm>Prev <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                   <sm>Current <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Change
                                   <br> 
                                   <sm>δ Base  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                   <sm>δ Prev  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Previous
                                   <br> 
                                   <sm class="pull-left" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                   <sm style="position: relative; bottom: 2px;"><i class="fa fa-flag text-warning"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                   <sm class="pull-right" style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Baseline
                                   <br> 
                                   <sm class="pull-left" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                   <sm style="position: relative; bottom: 2px;"><i class="fa fa-flag text-warning"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                   <sm class="pull-right" style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                                </th>
                                <th class="sorting">Status<br><br>
                                </th>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody>
                          @if(!empty($submissionsSummary))   
                              @foreach($submissionsSummary as $responseId=> $submission)
                                 @if($submission['status']=='missed')
                                    <tr>
                                      <td class="text-center">{{ $submission['patient'] }}</td>
                                       <td class="text-center">
                                         <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                         <sm ><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                      </td>
                                      
                                       <td class="text-center sorting">-</td>
                                       <td class="text-center sorting">-</td>
                                       <td class="text-center sorting">-</td>
                                    
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>-</h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>-</h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>-</h4>
                                        </td>

                                        <td class="text-right sorting text-error">-</td>
                                        <td class="text-center sorting text-warning">-</td>
                                        <td class="text-left sorting  text-success">-</td>
                                     
                                        <td class="text-right sorting text-error">-</td>
                                        <td class="text-center sorting text-warning">-</td>
                                        <td class="text-left sorting  text-success">-</td>
                                     
                                      <td class="text-center text-success">-</td>
                                      <td class="text-center text-success">-</td>
                                   </tr>
                                 @else 

                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
                                    <td class="text-center">{{ $submission['patient'] }}</td>
                                    <td class="text-center">
                                      <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                   </td>
                                   
                                      <td class="text-right sorting">{{ $submission['baseLineScore'] }}</td>
                                      <td class="text-center sorting">{{ $submission['previousScore'] }}</td>
                                      <td class="text-left sorting">{{ $submission['totalScore'] }}</td>
                                   
                                     <td class="text-right semi-bold margin-none flagcount p-h-0">
                                        <h4><b class="text-{{ $submission['totalBaseLineFlag'] }}">{{ $submission['comparedToBaslineScore'] }}</b></h4>
                                     </td>
                                     <td  class="text-center semi-bold margin-none flagcount p-h-0">
                                       <h4><b>/</b></h4>
                                     </td>
                                     <td  class="text-left semi-bold margin-none flagcount p-h-0">
                                        <h4><b class="f-w text-{{ $submission['totalPreviousFlag'] }}">{{ $submission['comparedToPrevious'] }}</b></h4>
                                     </td>

                                     <td class="text-right sorting text-error">{{ $submission['previousFlag']['red'] }}</td>
                                     <td class="text-center sorting text-warning">{{ $submission['previousFlag']['amber'] }}</td>
                                     <td class="text-left sorting text-success">{{ $submission['previousFlag']['green'] }}</td>
                                
                                     <td class="text-right sorting text-error">{{ $submission['baseLineFlag']['red'] }}</td>
                                     <td class="text-center sorting text-warning">{{ $submission['baseLineFlag']['amber'] }}</td>
                                     <td class="text-left sorting text-success">{{ $submission['baseLineFlag']['green'] }}</td>
                                  
                                   <td class="text-center text-success">{{ ucfirst($submission['status']) }}</td>
                                   <td class="text-center text-success">{{ ucfirst($submission['reviewed']) }}</td>
                                </tr>
                                @endif
                        
                            @endforeach
                        @else 
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif    
                                
                          </tbody>
                       </table>
                              <hr style="margin: 0px 0px 10px 0px;">
                              <div class="text-right {{ (empty($submissionsSummary))?'hidden':'' }}">
                                 <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
                              </div>
                           </div>
                        </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="grid simple grid-table grid-table-sort">
                               <div class="grid-title no-border">
                              <h4>Patient <span class="semi-bold">Summary</span> <sm class="light">(These is for the Cumulative Submissions)</sm></h4>
                              <!-- <div class="tools">
                               
                                 <div class="dataTables_filter pull-right filter2" id="example_filter"><input type="text" aria-controls="example" class="input-medium" placeholder="search by patient id"></div>
                              </div> -->
                           </div>
                           <div class="grid-body no-border" style="display: block;">
                              <table class="table table-flip-scroll table-hover">
                     <thead class="cf">
                        <tr>
                           <th width="12%">Patient ID</th>
                           <th width="31%">Total Submissions</th>
                           <th colspan="3" class="sorting">
                              Compared To Previous
                              <br> 
                              <sm class="pull-left" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                              <sm style="position: relative; bottom: 2px;"><i class="fa fa-flag text-warning"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                              <sm class="pull-right" style="margin-right: 20px"><i class="fa fa-flag text-success"></i> <!--  <i class="iconset top-down-arrow"></i> --></sm>
                           </th>
                           <th colspan="3" class="sorting">
                              Compared To Baseline
                              <br> 
                              <sm class="pull-left" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                              <sm style="position: relative; bottom: 2px;"><i class="fa fa-flag text-warning"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                              <sm class="pull-right" style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <!-- <i class="iconset top-down-arrow"></i> --></sm>
                           </th>
                           <th class="sorting">Graph <br> 
                               <sm>  <i class="fa fa-circle"></i> Baseline   &nbsp; &nbsp;<i class="fa fa-circle text-warning"></i> Total Score</sm>
                                       </th>
                       <!--     <th>
                              Action
                           </th> -->
                        </tr>
                     </thead>
                     <tbody>
                     <?php 
                          $i=1;
                        ?>
                  @if(!empty($patients))   
                     @foreach($patients as $patient)
                      <?php
                         if($i==6)
                              break;

                        $patientId = $patient['id'];
                        $status = $patient['account_status'];
                        $patientStatus = $patient['account_status'];
                        $referenceCode = $patient['reference_code'];

                        $status_class = 'text-success';
                        if($patient['account_status']=='suspended')
                            $status_class = 'text-error';
                        

                        if(!isset($patientResponses[$referenceCode])) //inactive patient data
                        {
                            $patientsSummary[$referenceCode]['lastSubmission'] = '-';
                            $patientsSummary[$referenceCode]['nextSubmission'] = '-';
                            $patientsSummary[$referenceCode]['completed'] = [];
                            $patientsSummary[$referenceCode]['missed'] = 0;
                            $patientsSummary[$referenceCode]['late'] = [];
                       
                        }
                        
                        $patientSummary = $patientResponses[$referenceCode];
                      ?>
                      
                        <tr>
                           <td onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">{{ $referenceCode }}</td>
                           <td  onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              <div class="lst-sub">
                                 <h2 class="bold pull-left">
                                    {{ count($patientSummary['completed']) }}<br>
                                    <sm class="text-success">Completed</sm>
                                 </h2>
                                 <h2 class="bold pull-left">
                                    {{ count($patientSummary['late']) }}<br>
                                    <sm class="text-warning">Late</sm>
                                 </h2>
                                 <h2 class="bold pull-left">
                                    {{ $patientSummary['missed'] }}<br>
                                    <sm class="text-danger">Missed</sm>
                                 </h2>
                                 <div class="pull-left p-t-20">
                                    <span class="sm-font">Last Submission  <b>{{ $patientSummary['lastSubmission'] }}</b></span><br>
                                    <span class="sm-font">Next Submission  <b>{{ $patientSummary['nextSubmission'] }}</b></span>
                                 </div>
                              </div>
                           </td>
                           <td class="text-right sorting text-error" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">                              
                              {{ $patientSummary['previousFlag']['red'] }}
                           </td>
                           <td class="text-center sorting text-warning" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">                              
                              {{ $patientSummary['previousFlag']['amber'] }}
                           </td>
                           <td class="text-left sorting text-success" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              {{ $patientSummary['previousFlag']['green'] }}
                           </td>

                           <td class="text-right sorting text-error" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              {{ $patientSummary['baseLineFlag']['red'] }}
                           </td>
                           <td class="text-center sorting text-warning" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              {{ $patientSummary['baseLineFlag']['amber'] }}
                           </td>
                           <td class="text-left sorting text-success" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              {{ $patientSummary['baseLineFlag']['green'] }}
                           </td>
                           <td onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              <div class="chart-block" style="padding:28px">
                                 <div id="chart_mini_{{ $patientId }}" style="vertical-align: middle; display: inline-block; width: 130px; height: 35px;"></div>
                              </div>
                           </td>
                           <!-- <td>
                              <span class="{{ $status_class }}"> {{ $status }}</span>
                           </td> -->
                        </tr>
                        <?php 
                          $i++;
                          ?>
                        @endforeach
                    @else 
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                    @endif 
                     </tbody>
                  </table>
                              <hr style="margin: 0px 0px 10px 0px;">
                              <div class="text-right {{ (empty($patients))?'hidden':'' }}">
                                 <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients') }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
 
<script type="text/javascript">
  var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
  var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 
   
   $(function () {
      $('[data-toggle="tooltip"]').tooltip();

       // submission chart
       var legends = {Baseline: "Baseline Flags",Previous: "Previous Flags"};
        lineChartWithOutBaseLine(<?php echo $projectFlagsChart['redFlags'];?>,legends,"chartdiv" ,'Project Submissions','Total Red Flags');

        $('select[name="generateChart"]').change(function (event) { 
            if($(this).val()=='unreviewed')
            { 
             var legends = {score: "Unreviewed Submission"};
              lineChartWithOutBaseLine(<?php echo $projectFlagsChart['unreviewedSubmission'];?>,legends,"chartdiv",'Project Submissions','Total Unreviewed Submission');
            }
            else if($(this).val()=='red_flags')
            { 
              legends = {Baseline: "Baseline Flags",Previous: "Previous Flags"};
              lineChartWithOutBaseLine(<?php echo $projectFlagsChart['redFlags'];?>,legends,"chartdiv",'Project Submissions','Total Red Flags');
            }
            else if($(this).val()=='amber_flags')
            {
              legends = {Baseline: "Baseline Flags",Previous: "Previous Flags"};
              lineChartWithOutBaseLine(<?php echo $projectFlagsChart['amberFlags'];?>,legends,"chartdiv",'Project Submissions','Total Amber Flags');

            }
            else if($(this).val()=='submissions')
            {
              legends = {completed: "Completed",late: "Late",missed: "Missed"};
              lineChartWithOutBaseLine(<?php echo $projectFlagsChart['patientsSubmission'];?>,legends,"chartdiv",'Project Submissions','Total Count');

            } 

          });


         })
    

   
      </script>
<style type="text/css">
         #chartdiv {
         width : 112%;
         height   : 250px;
         }                                                  
         .demo { position: relative; }
         .demo i {
         position: absolute; bottom: 10px; right: 24px; top: auto; cursor: pointer;
         }
      </style>
      <script type="text/javascript">
      
   $(document).ready(function() {
      <?php 
    foreach($patients as $patient)
    {
      $patientId = $patient['id'];
      $referenceCode = $patient['reference_code'];
                                          
      $chartData = (isset($patientMiniGraphData[$referenceCode]))?json_encode($patientMiniGraphData[$referenceCode]):'[]';
      ?>
      miniGraph(<?php echo $chartData; ?>,'chart_mini_{{ $patientId }}')
      <?php 
    } 
  ?>

   });
        
         
         var chart = AmCharts.makeChart( "piechart", {
         "type": "pie",
         "theme": "light",
           "dataProvider": [ {
               "title": "# Missed",
               "value": {{ $responseCount['missedCount'] }}
             }, {
               "title": "# Completed",
               "value": {{ $responseCount['completedCount'] }}
             } 
             , {
               "title": "# late",
               "value": {{ $responseCount['lateCount'] }}
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
         
 
      </script>
      <style type="text/css">
         .grid-title h4 {
         width: 52% !important;
         white-space: inherit;
         overflow: hidden;
         text-overflow: initial;
         }
         .bigger{
            font-size: 25px;
         }
         .top-data .col-md-2 {
             width: 33% !important;
         }
         .top-data .tiles {
             height: 120px;
          }


      </style>
@endsection