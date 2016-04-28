@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
         <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="#" class="active">Patients</a></li>       
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div>
   
   
   <div class="row">
     <div class="col-sm-4">
        <div class="page-title">
          <h3><span class="semi-bold">Patients</span></h3>
          <p>(Click on any Patient ID to see Profile Details)</p>
        </div>
     </div>
     <div class="col-sm-8 pull-right">
       <div class="m-t-10">
       
       <div class="pull-right">
       @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
       <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Patient</a>
       @endif
          
       
       </div>
       <div class="pull-right m-r-15 patient-search">
          <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
          <option value="">-select patient-</option>
           @foreach($allPatients as $patientData)
             <option  value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
           @endforeach
          </select>
       </div>
      <div class="pull-right m-r-15">
       <form name="searchData" method="GET"> 
      
       <input type="hidden" class="form-control" name="startDate"  >
       <input type="hidden" class="form-control" name="endDate"  >
          <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
             <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
             <span></span> <b class="caret"></b>
            
          </div>
           
       </form>
       <input type="hidden" name="flag" value="0">
      </div>
   </div>
     </div>
   </div>
</div>
<div class="grid simple">
   <div class="grid-body no-border table-data">
      <br>
      <div class="row">
         <div class="col-sm-6 ">
            <div class="tiles white">
               <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients?patients=created') }}">
                  <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                     <h4> <i class="fa fa-users"></i> Total Recruited Patients: <b class="bigger text-success pull-right">{{ $allpatientscount }} </b> </h4>
                     <hr>
                  </div>
               </a>
            </div>
         </div>
         <div class="col-sm-6">
            <div class="tiles white">
               <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients?patients=active') }}">
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
                  <div id="piechart" class="piechart-height"></div>
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
                     <sm class="light">(These are for the Cumulative Submissions)</sm>
                  </h4>
                  <!-- <div class="tools">
                  <label class="filter-label">Filter</label>
                  <form method="get" class="tools-form">  
                     <select name="patients" id="patients" class=" select2  form-control inline filterby pull-left -m-5">
                        <option value="">All</option>
                        <option {{ ($patientsStatus=='active')?'selected':''}} value="active">Active Patients</option>
                        <option {{ ($patientsStatus=='created')?'selected':''}} value="created">Recruited Patients</option>
                     </select>
                     <span class="cf-loader hidden patientFilter m-t-3"></span>
                  </form> -->
                  
                  
                     <!-- <div class="dataTables_filter pull-right filter2" id="example_filter"><input type="text" aria-controls="example" class="input-medium" placeholder="search by patient id"></div> -->
                  <!-- </div> -->
               </div>
               <div class="grid-body no-border" style="display: block;">
                 <table class="table table-flip-scroll table-hover">
                     <thead class="cf">
                       <tr>

                          <th width="11%">Patient ID</th>
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
                     <tbody id="patientSummaryData" limit="">
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
                             <div class="lst-sub submission-count">
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
                    <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                  @endif 
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

   drawPieChart("piechart",<?php echo  $pieChartData; ?>);
}); 






   $(document).ready(function() {

      $('select[name="patients"]').change(function (event) { 
        $(".patientFilter").removeClass('hidden');
         $('form').submit();
      });

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

  $('select[name="referenceCode"]').change(function (event) { 
        var referenceCode = $(this).val();
        if(referenceCode!='')
            window.location.href = BASEURL+"/patients/"+referenceCode; 
      });
  
   });
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
