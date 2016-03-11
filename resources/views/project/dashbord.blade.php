@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > Home</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div class="col-sm-8">
<div class="row">
                     <h1>Dashboard</h1>
                </div>
                  </div>
                  <div class="col-sm-4">
                  <div class="row">
                  @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
                     <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create') }}" class="btn btn-primary pull-right m-t-10"><i class="fa fa-plus"></i> Add Patient</a>
                  @endif
                  </div>
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
                  <div class="row">
            <div class="col-sm-6 ">
<div class="tiles white">
   <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients?patients=created') }}">
                           <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                          <h4> <i class="fa fa-users text-success m-r-5"></i> Total Recruited Patients: <b class="bigger text-success pull-right">{{ $allpatientscount }} </b> </h4>
                         </div>
   </a>                      
                         </div>
                     </div>
                     <div class="col-sm-6">
                     <div class="tiles white">
         <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients?patients=active') }}">
                           <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                            <h4> <i class="fa  fa-check-square-o text-success m-r-5"></i> Total Active Patients:  <b class="bigger text-success pull-right">{{ $activepatients }} </b></h4>
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

                                 <p class="p-t-10 m-0 text-muted prev-base">Previous / Baseline </p>
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


                                 <p class="p-t-10 m-0 text-muted prev-base">Previous / Baseline </p>
                                 <h2 class="m-0"><a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=amber&type=previous"><b class="grey">{{ $responseCount['amberPrevious'] }} </b></a>/<a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/flags?active=amber&type=baseline"><b class="f-w grey"> {{ $responseCount['amberBaseLine'] }}</b></a></h2>

                              </div>
                           </div>
                        </a>
                     </div>
                     <div class="col-md-2 ">
                        <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions?submissionStatus=unreviewed">
                           <div class="tiles white added-margin">
                              <div class="tiles-body p-17">
                                 <h5 class="bold m-b-30 m-t-0">Unreviewed Submissions <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="Submissions that have not been reviewed yet"></i></h5>
                                 <!-- <p class="prev-base">&nbsp;</p> -->
                                 <h2 class="bold">
                                 <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions?submissionStatus=unreviewed"><b class="grey">{{ $responseCount['unreviewedSubmission'] }}</b></a></h2>
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
                                 <div class="col-sm-6">
                                @if(!empty($submissionsSummary))
                                    <div class="row"><div id="piechart" class="piechart-height"></div></div>
                                @else 
                                    <div class="row text-center no-data-found" ><i class="fa fa-5x fa-frown-o"></i><br>No data found</div>
                                @endif
                                 </div>
                                 <div class="col-sm-6">
                                 <div class="row">
                                          <div class="col-sm-12 m-t-30">
                                          <div class="row">
                                             <div class="col-sm-4 text-center">
                                              <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions?submissionStatus=completed">
                                                <h2 class="bold m-0 inline">{{ $responseCount['completed'] }}%</h2>
                                                <p> # Completed</p>
                                                </a>
                                             </div>
                                             <div class="col-sm-4 text-center">
                                              <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions?submissionStatus=late">
                                                <h2 class="bold m-0 inline">{{ $responseCount['late'] }}%</h2>
                                                <p> # Late</p>
                                                </a>
                                             </div>
                                             <div class="col-sm-4 text-center">
                                                <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions?submissionStatus=missed">
                                                <h2 class="bold m-0 inline">{{ $responseCount['missed'] }}%</h2>
                                                <p> # Missed</p>
                                                </a>
                                             </div>
                                             </div>
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
                           <div class="grid-title no-border text-left">
                              <h4>Alerts <span class="semi-bold">Notification</span> <i class="fa fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Alerts Generated For Patients"></i></h4>
                        
                           </div>

                           <div class="grid-body no-border" style="display: block;">
                           @if(!empty($prejectAlerts['alertMsg']))
                            @foreach($prejectAlerts['alertMsg'] as $prejectAlert)
                              <div class="notification-messages {{ $prejectAlert['class'] }}" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $prejectAlert['responseId'] }}';">
                                <div class="message-wrapper msg-card">
                                     <div class="heading">Patient ID {{ $prejectAlert['patient'] }}   </div>
                                     <div class="description"> {{ sprintf($prejectAlert['msg'], $prejectAlert['sequenceNumber'] ) }} </div>
                                  </div>
                                  <!-- <div class="date pull-right"> Yesterday </div> -->
                                  <div class="clearfix"></div>
                              </div>
                            @endforeach
                            <div class="text-right">
                                 <a href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/notifications" class="text-success {{ ($prejectAlerts['alertCount']>4) ?'':'hidden'}}">View All <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;</a>
                              </div>
                          @else 
                             <div class="text-center text-muted"> <i class="fa fa-bell"></i> No New Notification</div>
                          @endif
<!--                            <div class="notification-messages info">
 
                             

       <div class="message-wrapper msg-card">
         <div class="heading"> New Patient ID Generated </div>
         <div class="description"> These are scores & flags for current submissions These are scores & flags for current submissions </div>
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
   </div> -->

                              
                              
                           </div>
                        </div>
         </div>
      </div>




                  <br>
                  <div class="grid simple ">
                          <div class="grid-title no-border">
                            <div class="row health-tracker-grid">
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
                          </div>
                           <div class="grid-body no-border table-data ">
                            @if(!empty($submissionsSummary))                              
                              <div id="chartdiv" style="width:100%; height: 400px;"></div>
                            @else 
                              <div class="text-center no-data-found" ><br><br><br><i class="fa fa-5x fa-frown-o"></i><br>No data found</div>
                            @endif
                           </div>
                        </div>
                  <div class="grid simple grid-table">
                           <div class="grid-title no-border">
                        <h4>
                          Submissions <span class="semi-bold">Summary</span> 
                          <sm class="light">(These are scores & flags for current submissions)</sm>
                       </h4>
                           </div>
                           <div class="grid-body no-border" style="display: block;">
                              <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
                          <thead class="cf">
                             <tr>
                                <th class="sorting" width="16%">Patient ID <br><br></th>
                                <th class="sorting sortSubmission" sort="createdAt" sort-type="asc"  style="cursor:pointer;"># Submission <i class="fa fa-angle-down sortCol"></i><br><br></th>
                                <th colspan="3" class="sorting">
                                   Total Score
                                   <br> 
                                   <sm class="sortSubmission" sort="baseLineScore" sort-type="asc">Base <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="sortSubmission" sort="previousScore" sort-type="asc">Prev <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="sortSubmission" sort="totalScore" sort-type="asc">Current <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Change
                                   <br> 
                                   <sm class="sortSubmission" sort="comparedToBaseLine" sort-type="asc">δ Base  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="sortSubmission" sort="comparedToPrevious" sort-type="asc">δ Prev  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Previous
                                   <br> 
                                   <sm class="pull-left sortSubmission" sort="previousTotalRedFlags" sort-type="asc" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm style="position: relative; bottom: 2px;" class="sortSubmission" sort="previousTotalAmberFlags" sort-type="asc"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="pull-right sortSubmission" sort="previousTotalGreenFlags" sort-type="asc" style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Baseline
                                   <br> 
                                   <sm class="pull-left sortSubmission" sort="baseLineTotalRedFlags" sort-type="asc" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm style="position: relative; bottom: 2px;"  class="sortSubmission" sort="baseLineTotalAmberFlags" sort-type="asc"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="pull-right sortSubmission" sort="baseLineTotalGreenFlags" sort-type="asc" style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th class="sorting">Status<br><br>
                                </th>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody id="submissionData" limit="5" object-type="submission" object-id="0">
                          <div class="loader-outer hidden">
                            <span class="cf-loader"></span>
                         </div>
                          @if(!empty($submissionsSummary))   
                              @foreach($submissionsSummary as $responseId=> $submission)
                                 @if($submission['status']=='missed' || $submission['status']=='late')
                                    <tr>
                                      <td class="text-center">{{ $submission['patient'] }}</td>
                                       <td class="text-center">
                                         <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                         <sm ><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                      </td>
                                      
                                       <td class="text-center sorting">0</td>
                                       <td class="text-center sorting">0</td>
                                       <td class="text-center sorting">0</td>
                                    
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>-</h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>/</h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>-</h4>
                                        </td>

                                        <td class="text-right sorting text-error">0</td>
                                        <td class="text-center sorting text-warning">0</td>
                                        <td class="text-left sorting  text-success">0</td>
                                     
                                        <td class="text-right sorting text-error">0</td>
                                        <td class="text-center sorting text-warning">0</td>
                                        <td class="text-left sorting  text-success">0</td>
                                     
                                      <td class="text-center text-success">{{ ucfirst($submission['status']) }}</td>
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
                        <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
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
                              <h4>Patient <span class="semi-bold">Summary</span> <sm class="light">(This is for the Cumulative Submissions)</sm></h4>
                              <!-- <div class="tools">
                               
                                 <div class="dataTables_filter pull-right filter2" id="example_filter"><input type="text" aria-controls="example" class="input-medium" placeholder="search by patient id"></div>
                              </div> -->
                           </div>
                           <div class="grid-body no-border" style="display: block;">
                              <table class="table table-flip-scroll table-hover">
                     <thead class="cf">
                        <tr>

                          <th width="12%">Patient ID</th>
                          <th width="22%" class="sorting">Total Submissions<br>
                              <sm class="sortPatientSummary" sort="completed" sort-type="asc" >Completed <i class="fa fa-angle-down sortCol"></i></sm>
                              <sm class="sortPatientSummary" sort="late" sort-type="asc" >Late <i class="fa fa-angle-down sortCol"></i></sm>
                              <sm class="sortPatientSummary" sort="missed" sort-type="asc" >Missed <i class="fa fa-angle-down sortCol"></i></sm>
                          </th>
                           <th width="17%"></th>
                           <th colspan="3" class="sorting">
                              Compared To Previous
                              <br> 
                              <sm class="pull-left sortPatientSummary" sort="previousTotalRedFlags" sort-type="asc"  style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                              <sm class="sortPatientSummary" sort="previousTotalAmberFlags" sort-type="asc" style="position: relative; bottom: 2px;"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                              <sm class="pull-right sortPatientSummary" sort="previousTotalGreenFlags" sort-type="asc"  style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                           </th>
                           <th colspan="3" class="sorting">
                              Compared To Baseline
                              <br> 
                              <sm class="pull-left sortPatientSummary" sort="baseLineTotalRedFlags" sort-type="asc"  style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                              <sm class="sortPatientSummary" sort="baseLineTotalAmberFlags" sort-type="asc" style="position: relative; bottom: 2px;"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                              <sm class="pull-right sortPatientSummary" sort="baseLineTotalGreenFlags" sort-type="asc"  style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                           </th>
                           <th class="sorting">Graph <br> 
                               <sm>  <i class="fa fa-circle baseline-color"></i> Baseline   &nbsp; &nbsp;<i class="fa fa-circle theme-color"></i> Total Score</sm>
                                       </th>
                       <!--     <th>
                              Action
                           </th> -->
                        </tr>
                     </thead>
                     <tbody id="patientSummaryData" limit="5">
                      <div class="loader-outer hidden">
                            <span class="cf-loader"></span>
                         </div>
                     <?php 
    
                   foreach ($patients as  $patient) {
                    $patientReferenceCode[] = $patient['reference_code'];
                    $patientIds[$patient['reference_code']] = $patient['id'];
                  }
                     
                      ?>
                  @if(!empty($patientSortedData)) 
                     @foreach($patientSortedData as $referenceCode => $data)
                     
                       <?php
                        $patientId = $patientIds[$referenceCode];    
                        $status_class='';
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
                                 <h2 class="bold inline">
                                    {{ $patientSummary['completed'] }}<br>
                                    <sm class="text-success">Completed</sm>
                                 </h2>
                                 <h2 class="bold inline">
                                    {{ $patientSummary['late'] }}<br>
                                    <sm class="text-warning">Late</sm>
                                 </h2>
                                 <h2 class="bold inline">
                                    {{ $patientSummary['missed'] }}<br>
                                    <sm class="text-danger">Missed</sm>
                                 </h2>
                                 
                              </div>
                           </td>
                           <td>
                           <div class="lst-sub text-center p-t-20">
                                    <span class="sm-font">Last Submission  <b>{{ $patientSummary['lastSubmission'] }}</b></span><br>
                                    <span class="sm-font">Next Submission  <b>{{ $patientSummary['nextSubmission'] }}</b></span>
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
              
                        </tr>
                         
                        @endforeach
                    @else 
                        <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
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

             "radius": "36%",
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