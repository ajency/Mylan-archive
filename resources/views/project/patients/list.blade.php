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
<div>
   <div class="pull-right m-t-25">
      <a href="#" class="btn btn-danger hidden"><i class="fa fa-download"></i> Download CSV</a>
      <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Patient</a>
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
   <div class="page-title">
      <h3><span class="semi-bold">Patients</span></h3>
      <p>(Click on any Patient ID to see Profile Details)</p>
   </div>
</div>
<div class="grid simple">
   <div class="grid-body no-border table-data">
      <br>
      <div class="row">
         <div class="col-sm-6 ">
            <div class="tiles white">
               <a href="patients-Recruited.html">
                  <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                     <h4> <i class="fa fa-users"></i> Total Recruited Patients: <b class="bigger text-success pull-right">{{ count($patients) }} </b> </h4>
                     <hr>
                  </div>
               </a>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="tiles white">
               <a href="patients-active.html">
                  <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                     <h4> <i class="fa fa-users"></i> Total Active Patients:  <b class="bigger text-success pull-right">{{ $activepatients }} </b></h4>
                     <hr>
                  </div>
               </a>
            </div>
         </div>
      </div>
      <div class="row ">
         <div class="col-md-5">
            <div class="tiles white added-margin">
               <div class="tiles-body">
                  <div id="piechart"></div>
               </div>
            </div>
         </div>
         <div class="col-md-7 m-t-60 ">
            <div class="col-md-4 text-center ">
               <h3 class="no-margin bold">{{ $completed }}%</h3>
               <p class=" text-underline">{{ $completedCount }} Submissions Completed</p>
            </div>
            <div class="col-md-4 text-center">
               <h3 class="no-margin bold">{{ $late }}%</h3>
               <p class="">{{ $lateCount }} Submissions Late</p>
            </div>
            <div class="col-md-4 text-center">
               <h3 class="no-margin bold">{{ $missed }}%</h3>
               <p class="">{{ $missedCount }} Submissions Missed</p>
            </div>
         </div>
      </div>
      <br>
      <div class="row">
         <div >
            <div class="grid simple grid-table grid-table-sort">
               <div class="grid-title no-border">
                  <h4>
                     Patient <span class="semi-bold">Summary</span> 
                     <sm class="light">( This is for the Cumulative Submissions )</sm>
                  </h4>
                  <div class="tools">
                     <select name="role" id="role" class=" select2  form-control inline filterby pull-left -m-5">
                        <option value="2">Filter By</option>
                        <option value="2">Active Patients</option>
                        <option value="2">Recruited Patients</option>
                     </select>
                     <div class="dataTables_filter pull-right filter2" id="example_filter"><input type="text" aria-controls="example" class="input-medium" placeholder="search by patient id"></div>
                  </div>
               </div>
               <div class="grid-body no-border" style="display: block;">
                  <table class="table table-flip-scroll table-hover">
                     <thead class="cf">
                        <tr>
                           <th width="12%">Patient ID</th>
                           <th width="31%">Total Submissions</th>
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
                           <th>
                              Action
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                     @foreach($patients as $patient)
                      <?php
                        $patientId = $patient['id'];
                        $status = $patient['account_status'];
                        $patientStatus = $patient['account_status'];
                        $referenceCode = $patient['reference_code'];

                        $status_class = 'text-success';
                        if($patient['account_status']=='suspended')
                            $status_class = 'text-error';
                        

                        if(!isset($patientsSummary[$referenceCode])) //inactive patient data
                        {
                            $patientsSummary[$referenceCode]['lastSubmission'] = '-';
                            $patientsSummary[$referenceCode]['nextSubmission'] = '-';
                            $patientsSummary[$referenceCode]['completed'] = [];
                            $patientsSummary[$referenceCode]['missed'] = 0;
                            $patientsSummary[$referenceCode]['late'] = [];
                       
                        }
                        
                        $patientSummary = $patientsSummary[$referenceCode];
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
                           <td class="text-center sorting" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              <span class=" text-error">
                               
                              {{ $patientSummary['previousFlag']['red'] }}
                              </span>
                              <span class="text-warning">
                     
                                {{ $patientSummary['previousFlag']['amber'] }}
                     
                              </span>
                              <span class="text-success">
                                
                                {{ $patientSummary['previousFlag']['green'] }}
                               
                              </span>
                           </td>
                           <td class="text-center sorting" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              <span class=" text-error">                         
                                  {{ $patientSummary['baseLineFlag']['red'] }}
                               
                              </span>
                              <span class="text-warning">
                                  {{ $patientSummary['baseLineFlag']['amber'] }}
                                  
                              </span>
                              <span class="text-success">
                         
                                  {{ $patientSummary['baseLineFlag']['green'] }}
                              
                              </span>
                           </td>
                           <td onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
                              <div class="chart-block" style="padding:28px">
                                 <div id="line1" style="vertical-align: middle; display: inline-block; width: 100px; height: 30px;"></div>
                              </div>
                           </td>
                           <td>
                              <span class="{{ $status_class }}"> {{ $status }}</span>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
                 
<script type="text/javascript">    
var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

$(document).ready(function() {
   $('.input-daterange input').datepicker({
       format: 'dd-mm-yyyy'
   }); 
}); 

var chart = AmCharts.makeChart( "piechart", {
  "type": "pie",
  "theme": "light",
     "dataProvider": [ {
               "title": "# Missed",
               "value": {{ $missedCount }}
             }, {
               "title": "# Completed",
               "value": {{ $completedCount }}
             } 
             , {
               "title": "# late",
               "value": {{ $lateCount }}
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
         width: 57% !important;
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
             height: 115px;
          }



      </style>

@endsection
