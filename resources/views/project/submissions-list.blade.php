@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
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
                           <div class="col-md-5">
                              <div class="tiles white added-margin">
                                 <div class="tiles-body">
                                    <div id="submissionschart"></div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-7 m-t-40 "><!-- b-r -->
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
                           <!-- <div class="col-md-3">
                              <div class="tiles white added-margin " style="zoom: 1;">
                                 <div class="tiles-body">
                                    <div class="tiles-title"> Avg Review Time </div>
                                    <div class="__web-inspector-hide-shortcut__">
                                       <h1 class="text-error bold inline no-margin"> {{ $avgReviewTime }} hrs</h1>
                                    </div>
                                    <p class="text-black">Average time taken for a submission  to <br>be reviewed after it has been submitted.</p>
                                 </div>
                              </div>
                           </div> -->
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
                          <table class="table table-flip-scroll table-hover dashboard-tbl sort-table class='sortable'">
                          <thead class="cf">
                                       <tr class="table-border-none">
                                       <th width="5%"> Patient Id</th>
                                       <th width="10%" class="text-left"> # Submissions</th>
                                       <th width="20%" class="text-center " colspan="3">
                                             Total Score
                                       </th>
                                     
                                       <th width="18%"  colspan="3" class="text-center">
                                          Change
                                        
                                       </th>
                                       <th width="15%" colspan="3" class="text-center">
                                          Previous
                                         
                                       </th>
                                       <th width="15%" colspan="3" class="text-center">
                                          Baseline
                                          
                                       </th>
                                       
                                       <th width="45%" class="text-center"> Status
                                       </th>
                                       <th width="15%" class="text-center"> Review Status
                                       </th>
                                    </tr>
                                    <tr class="md-size">
                                       <th width="10%" class="no-sort"></th>
                                       <th width="20%" class="no-sort"></th>
                                       <th  class="text-right ">
                                             Base
                                       </th>
                                        
                                       <th  class="text-center ">
                                            Prev
                                       </th>
                                       <th  class="text-left ">
                                    
                                         Current 
                                       </th>
                                       <th  class="text-right no-sort">
                                          δ Base  
                                       </th>
                                       <th class="no-sort"></th>
                                       <th  class="text-left no-sort">
                                         δ Prev  
                                        </th>
                                       <th class="text-center th-flag-outer">
                                          <i class="fa fa-flag text-error "></i>  
                                       </th>
                                         <th  class="text-center th-flag-outer">
                                          <i class="fa fa-flag text-warning"></i>  
                                       </th>
                                        <th  class="text-center th-flag-outer">
                                        <i class="fa fa-flag text-success"></i> 
                                       </th>
                                       <th  class="text-center th-flag-outer">
                                         
                                         <i class="fa fa-flag text-error"></i> 
                                         
                                       </th>
                                       <th class="text-center th-flag-outer">
                                         
                                          <i class="fa fa-flag text-warning"></i>  
                                      
                                       </th>
                                       <th class="text-center th-flag-outer">
                
                                          <i class="fa fa-flag text-success"></i> 
                                       </th>
                                       <th class="no-sort">
                                       </th>

                                    </tr>
                                 </thead>
                          <tbody>
                          @if(!empty($submissionsSummary))     
                              @foreach($submissionsSummary as $responseId=> $submission)
                                 @if($submission['status']=='missed')
                                    <tr>
                                      <td class="text-center">{{ $submission['patient'] }}</td>
                                       <td>
                                         <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                         <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                      </td>
                                        
                                       <td class="text-center sorting">-</td>
                                       <td class="text-center sorting">-</td>
                                       <td class="text-center sorting">-</td>
                                        
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>
                                              -
                                           </h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>
                                              -
                                           </h4>
                                        </td>
                                        <td class="text-center semi-bold margin-none flagcount p-h-0">
                                           <h4>
                                              -
                                           </h4>
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
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif    
                                
                          </tbody>
                       </table>
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

       "radius": "42%",
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

   });
</script>   


@endsection

