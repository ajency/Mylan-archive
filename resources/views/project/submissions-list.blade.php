@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="#" class="active">Submissions</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


<div class="page-title">
                     <h3><span class="semi-bold">Submissions</span></h3>
                     <!-- <p>(Click on any Patient ID to see Profile Details)</p> -->

                     <div class="patient-search pull-right">
                       <form name="searchData" method="GET"> 
                       <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
                          <option value="">-select patient-</option>
                           @foreach($allPatients as $patient)
                             <option   value="{{ $patient['id'] }}">{{ $patient['reference_code'] }}</option>
                           @endforeach
                          </select> 
                     </form>
                    </div>
                  </div>
                  <div class="grid simple">
                     <div class="grid-body no-border table-data">
                        <br>
                        <div class="row">
                           <!-- <div class="col-sm-6"> <h3 class="bold m-0">Submissions</h3></div> -->
                           <div class="col-sm-2">
                           </div>
                           <div class="col-sm-4 pull-right">
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
                        <hr>
                        <div class="row ">
                           <div class="col-md-4">
                              <div class="tiles white added-margin">
                                 <div class="tiles-body">
                                  @if(!empty($submissionsSummary))     
                                    <div id="submissionschart"></div>
                                  @else 
                                    <div class="text-center no-data-found" ><br><br><br><i class="fa fa-5x fa-frown-o"></i><br>No data found</div>
                                  @endif
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-5 m-t-40 "><!-- b-r -->
                              <div class="col-md-4 text-center ">
                                 <h3 class="no-margin bold">{{  $responseRate['completed'] }}%</h3>
                                 <p class=" text-underline">{{  $responseRate['completedCount'] }} Submissions Completed</p>
                              </div>
                              <div class="col-md-4 text-center">
                                 <h3 class="no-margin bold">{{  $responseRate['late'] }}%</h3>
                                 <p class="">{{  $responseRate['lateCount'] }} Submissions Late</p>
                              </div>
                              <div class="col-md-4 text-center">
                                 <h3 class="no-margin bold">{{  $responseRate['missed'] }}%</h3>
                                 <p class="">{{  $responseRate['missedCount'] }} Submissions Missed</p>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="tiles white added-margin " style="zoom: 1;">
                                 <div class="tiles-body">
                                    <div class="tiles-title"> Avg Review Time </div>
                                    <div class="__web-inspector-hide-shortcut__">
                                       <h1 class="text-error bold inline no-margin"> {{ round($avgReviewTime) }} hrs</h1>
                                    </div>
                                    <p class="text-black">Average time taken for a submission  to <br>be reviewed after it has been submitted.</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <hr>
                        <!-- Chart - Added -->
                        <div class="row">
                           <div class="col-md-9"></div>
                           <div class="col-md-3 text-right filter-dropdown submission-filter">
                              <span class="cf-loader hidden submissionFilter"></span>
                              <form method="get"> 
                              <label class="filter-label m-t-15 m-r-10">Filter</label>                              
                             <select name="submissionStatus" id="submissionStatus" class="pull-right select2 m-t-5 m-b-20 form-control inline filterby pull-right">
                                <option value="all">All</option>
                                <option {{ ($submissionStatus=='completed')?'selected':'' }} value="completed">Completed</option>
                                <option {{ ($submissionStatus=='late')?'selected':'' }} value="late">Late</option>
                                <option {{ ($submissionStatus=='missed')?'selected':'' }} value="missed">Missed</option>
                                <option {{ ($submissionStatus=='unreviewed')?'selected':'' }} value="unreviewed">Unreviewed</option>
                                <!-- <option {{ ($submissionStatus=='missed')?'selected':'' }} value="missed">Missed</option> -->
                             </select>
                             
                             </form>
                             
                           </div>
                          <!-- <div class="col-md-3 text-right">
                               <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;"> 
                           </div>-->
                        </div>
                        <div class="alert alert-info alert-black">
                           Submission Summary
                           <sm class="light">(These are scores & flags for current submissions)</sm>
                        </div>
                        <div class="grid-body no-border" style="display: block;">
                          <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="status" cond="{{ $submissionStatus }}">
                          <thead class="cf">
                             <tr>
                                <th class="sorting" width="16%">Patient ID <br><br></th>
                                <th class="sorting sortSubmission" sort="sequenceNumber" sort-type="asc"  style="cursor:pointer;"># Submission <i class="fa fa-angle-down sortCol"></i><br><br></th>
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
                                <th class="sorting sortSubmission" sort="status" sort-type="asc"  style="cursor:pointer;">Status <i class="fa fa-angle-down sortCol"></i><br><br>
                                </th>
                                <th class="sorting sortSubmission" sort="reviewed" sort-type="asc"  style="cursor:pointer;">Review Status <i class="fa fa-angle-down sortCol"></i><br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody id="submissionData" limit="" object-type="submission" object-id="0">
                          <div class="loader-outer hidden">
                            <span class="cf-loader"></span>
                         </div>
                          @if(!empty($submissionsSummary))     
                              @foreach($submissionsSummary as $responseId=> $submission)
                                 @if($submission['status']=='missed' || $submission['status']=='late')
                                    <tr>
                                      <td class="text-center">{{ $submission['patient'] }}</td>
                                       <td>
                                         <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                         <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                      </td>
                                        
                                       <td class="text-center sorting">0</td>
                                       <td class="text-center sorting">0</td>
                                       <td class="text-center sorting">0</td>
                                        
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>
                                              -
                                           </h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>
                                             /
                                           </h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>
                                             -
                                           </h4>
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
                                    <td>
                                      <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                   </td>
                                   
                                    <td class="text-right sorting">{{ $submission['baseLineScore'] }}</td>
                                    <td class="text-center sorting">{{ $submission['previousScore'] }}</td>
                                    <td class="text-left sorting">{{ $submission['totalScore'] }}</td>

                                    <td class="text-right semi-bold margin-none flagcount p-h-0">
                                      <h4>
                                         <b class="text-{{ $submission['totalBaseLineFlag'] }}">{{ $submission['comparedToBaslineScore'] }}</b>
                                      </h4>
                                    </td>
                                    <td class="text-center semi-bold margin-none flagcount p-h-0">
                                      <h4><b>/</b></h4>
                                    </td>
                                    <td class="text-left semi-bold margin-none flagcount p-h-0">
                                      <h4>
                                        <b class="f-w text-{{ $submission['totalPreviousFlag'] }}">{{ $submission['comparedToPrevious'] }}</b>
                                      </h4>
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
                       </div>
                        <hr style="    margin: 0px 0px 10px 0px;">
                     </div>
                  </div>
                  
<script type="text/javascript">
var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

 var chart = AmCharts.makeChart( "submissionschart", {
       "type": "pie",
       "theme": "light",
       "dataProvider": [ {
         "title": "# Missed",
         "value": {{ $responseRate['missedCount'] }}
       }, {
         "title": "# Completed",
         "value": {{ $responseRate['completedCount'] }}
       } 
       , {
         "title": "# late",
         "value": {{ $responseRate['lateCount'] }}
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
     } );// Pie Chart

$(document).ready(function() {

      $('select[name="submissionStatus"]').change(function (event) { 
         $(".submissionFilter").removeClass('hidden');
         $('form').submit();
      });

      $('select[name="referenceCode"]').change(function (event) { 
        var referenceCode = $(this).val();
        if(referenceCode!='')
            window.location.href = BASEURL+"/patients/"+referenceCode; 
      });

   });
</script>   


@endsection

