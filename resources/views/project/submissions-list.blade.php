@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Submissions</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<a class="btn btn-primary pull-right" id="btnSave" title="Download this page as a printable PDF"><i class="fa fa-print"></i> Get PDF
<span class="addLoader"></span></a>
<div class="page-title">
                     <h3><span class="semi-bold">Submissions</span></h3>
                     <!-- <p>(Click on any Patient ID to see Profile Details)</p> -->

                     <div class="patient-search pull-right m-r-15">
                       <form name="searchData" method="GET"> 
                       <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
                          <option class="ttuc" value="">-select patient-</option>
                           @foreach($allPatients as $patient)
                             <option class="ttuc patient-refer{{ $patient['reference_code'] }}"  value="{{ $patient['id'] }}">{{ $patient['reference_code'] }}</option>
                           @endforeach
                          </select> 
                     </form>
                    </div>
                  </div>
                  <div class="grid simple" id="page1">
                     <div class="grid-body no-border table-data">
                        <br>
                        <div class="row">
                           <!-- <div class="col-sm-6"> <h3 class="bold m-0">Submissions</h3></div> -->
                           <!--div class="col-sm-2">
                           </div-->
                           <div class="col-sm-4" style="margin:-10px 0 -10px 0;">
                              <div class="tiles white">
                                <a href="#">
                                  <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                                    <h4>Total Reviewed Submissions: <b class="bigger text-success pull-right">{{ $CounterData['reviewedCounts'] }}</b> </h4>
                                  </div>
                                </a>                      
                              </div>
                            </div>
                            <div class="col-sm-4" style="margin:-10px 0 -10px 0;">
                              <div class="tiles white">
                                <a href="#">
                                  <div class="tiles-body" style="    padding: 6px 18px 6px 24px;">
                                    <h4>Total Unreviewed Submissions:  <b class="bigger text-success pull-right">{{ $CounterData['unreviewedCounts'] }}</b></h4>
                                  </div>
                                </a>
                              </div>
                            </div>
                            
                           <div class="col-sm-4 pull-right">
                             <form name="searchData" method="GET"> 
                               <input type="hidden" class="form-control" name="startDate"  >
                               <input type="hidden" class="form-control" name="endDate"  >
                                  <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
                                     <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                     <span></span> <b class="caret"></b>
                                  </div>
                                <input type="hidden" class="form-control" name="submissionStatus" value="{{ $submissionStatus }}" >

                               </form>
                               <input type="hidden" name="flag" value="0">
                           </div>
                        </div>
                        <hr>
                        <div class="row ">
                           <div class="col-md-4">
                              <div class="tiles white added-margin">
                                 <div class="tiles-body">
                                  
                                    <div id="submissionschart"></div>
                                  
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-5 m-t-40 "><!-- b-r -->
                              <div class="col-md-4 text-center ">
                                 <h3 class="no-margin bold">{{  $patientsSummary['completed'] }}%</h3>
                                 <p class=" text-underline">{{  $patientsSummary['completedCount'] }} Submissions Completed</p>
                              </div>
                              <div class="col-md-4 text-center">
                                 <h3 class="no-margin bold">{{  $patientsSummary['late'] }}%</h3>
                                 <p class="">{{  $patientsSummary['lateCount'] }} Submissions Late</p>
                              </div>
                              <div class="col-md-4 text-center">
                                 <h3 class="no-margin bold">{{  $patientsSummary['missed'] }}%</h3>
                                 <p class="">{{  $patientsSummary['missedCount'] }} Submissions Missed</p>
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
                                <option {{ ($submissionStatus=='reviewed_no_action')?'selected':''}} value="reviewed_no_action">Reviewed - No action {{ ($project['reviewed_no_action'])?$project['reviewed_no_action']:'' }}</option>

                                <option {{ ($submissionStatus=='reviewed_call_done')?'selected':''}} value="reviewed_call_done">Reviewed - Call done {{ ($project['reviewed_call_done'])?$project['reviewed_call_done']:'' }}</option>

                                <option {{ ($submissionStatus=='reviewed_appointment_fixed')?'selected':''}} value="reviewed_appointment_fixed">Reviewed - Appointment fixed {{ ($project['reviewed_appointment_fixed'])?$project['reviewed_appointment_fixed']:'' }}</option>

                                <option {{ ($submissionStatus=='unreviewed')?'selected':'' }} value="unreviewed">Unreviewed {{ ($project['unreviewed'])?$project['unreviewed']:'' }}</option>
                                <!-- <option {{ ($submissionStatus=='missed')?'selected':'' }} value="missed">Missed</option> -->
                             </select>
                             <input type="hidden" class="form-control" name="startDate" value="{{ $startDate }}"  >
                             <input type="hidden" class="form-control" name="endDate" value="{{ $endDate }}" >
                             </form>
                             
                           </div>
                          <!-- <div class="col-md-3 text-right">
                               <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;"> 
                           </div>-->
                        </div>
                        <div class="alert alert-info alert-black cust-alert">
                           Submission Summary
                           <sm class="light">(These are scores & flags for current submissions)</sm>
                        </div>
                        <div class="grid-body no-border" style="display: block;padding: 10px 5px;">
                          <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="status" cond="{{ $submissionStatus }}">
                          <thead class="cf">
                             <tr>
                                <th class="sorting" width="8%">Patient ID <br><br></th>
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
                                   <sm class="pull-left sortSubmission" sort="previousTotalRedFlags" sort-type="asc" style="margin-left: 5px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm style="position: relative; bottom: 2px;" class="sortSubmission" sort="previousTotalAmberFlags" sort-type="asc"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="pull-right sortSubmission" sort="previousTotalGreenFlags" sort-type="asc" style="margin-right: 5px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Baseline
                                   <br> 
                                   <sm class="pull-left sortSubmission" sort="baseLineTotalRedFlags" sort-type="asc" style="margin-left: 5px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm style="position: relative; bottom: 2px;"  class="sortSubmission" sort="baseLineTotalAmberFlags" sort-type="asc"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="pull-right sortSubmission" sort="baseLineTotalGreenFlags" sort-type="asc" style="margin-right: 5px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th class="sorting sortSubmission" sort="alert" sort-type="asc"  style="cursor:pointer;">Alerts <i class="fa fa-angle-down sortCol"></i><br><br>
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
                          <?php
                            $firstBreak = 0;
                            $firstBreakCapture = 0;
                            $addClass = "";
                          ?>       
                              @foreach($submissionsSummary as $responseId=> $submission)
                                <?php
                                  $firstBreak = $firstBreak +1;
                                  if($firstBreakCapture == 0){
                                    if($firstBreak == 3){
                                       $addClass = "printPdfMargin"; 
                                       $firstBreakCapture = 1;
                                       $firstBreak = 0;
                                    }else{
                                        $addClass = "";
                                    }
                                  }else{
                                    if($firstBreak == 12){
                                       $addClass = "printPdfMargin"; 
                                       $firstBreak = 0;
                                    }else{
                                        $addClass = "";
                                    }

                                  }
                                  ?>
                                 @if($submission['status']=='missed' || $submission['status']=='late')
                                    <tr class="<?php echo $addClass; ?>">
                                      <td class="text-center ttuc patient-refer{{ $submission['patient'] }}">{{ $submission['patient'] }}</td>
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
                          
                                      <td class="text-center text-success">-</td>
                                      <td class="text-center text-success">{{ getStatusName($submission['status']) }}</td>
                                      <td class="text-center text-success">-</td>
                                   </tr>
                                 @else 

                                 <tr class="<?php echo $addClass; ?>" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
                                    <td class="text-center ttuc patient-refer{{ $submission['patient'] }}">{{ $submission['patient'] }}</td>
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
                                  
                                  <td class="text-center text-success">{{ $submission['alert'] }}</td> 
                                   <td class="text-center text-success">{{ getStatusName($submission['status']) }}</td>
                                   <td class="text-center text-success">
                                   <!-- <div class="submissionStatus" @if(strlen($submission['reviewed']) >10 ) data-toggle="tooltip" @endif data-placement="top" title="{{ getStatusName($submission['reviewed']) }}">{{ getStatusName($submission['reviewed']) }}</div> -->
                                   <div class="submissionStatus more-text">{{ getStatusName($submission['reviewed']) }} {{ ($project[$submission['reviewed']])?$project[$submission['reviewed']]:'' }}</div>

                                   </td>
                                </tr>
                                @endif
                        
                            @endforeach
                           @else 
                        <tr><td class="text-center no-data-found" colspan="20"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
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

 
$(document).ready(function() {
    drawPieChart("submissionschart",<?php echo  $patientsSummary['pieChartData']; ?>,1);

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


 //pdf
   $(function() { 
      $("#btnSave").click(function() { 
      //convert all svg's to canvas
      $(".table tr.printPdfMargin td").addClass("print-pdf-marginS");
      $(".table tr.printPdfMarginE td").addClass("print-pdf-marginSE");
      $(".addLoader").addClass("cf-loader");

     var svgTags = document.querySelectorAll('#dashboardblock svg');
      for (var i=0; i<svgTags.length; i++) {
        var svgTag = svgTags[i];
        var c = document.createElement('canvas');
        c.width = svgTag.clientWidth;
        c.height = svgTag.clientHeight;
        svgTag.parentNode.insertBefore(c, svgTag);
        svgTag.parentNode.removeChild(svgTag);
        var div = document.createElement('div');
        div.appendChild(svgTag);
        canvg(c, div.innerHTML);
      }
      html2canvas($("#page1"), {
          background: '#FFFFFF',
              onrendered: function(canvas) {
                var imgData = canvas.toDataURL("image/jpeg", 1.0);  
                var imgWidth = 290; 
                var pageHeight = 225;  
                var imgHeight = canvas.height * imgWidth / canvas.width;
                var heightLeft = imgHeight;

                var doc = new jsPDF('l', 'mm');
                var position = 0;

                doc.addImage(imgData, 'JPEG', 3, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                  position = heightLeft - imgHeight;
                  doc.addPage();
                  doc.addImage(imgData, 'JPEG', 3, position, imgWidth, imgHeight);
                  heightLeft -= pageHeight;
                }
                doc.save( 'Submissions.pdf');﻿
             }
          });
            setInterval(function(){ 
              $(".addLoader").removeClass("cf-loader"); 
                  $(".table tr.printPdfMargin td").removeClass("print-pdf-marginS");
                  $(".table tr.printPdfMarginE td").removeClass("print-pdf-marginSE");
            }, 3000);   
      });
    });

   // more-less
  $(document).ready(function() {
      // Configure/customize these variables.
      var showChar = 25;  // How many characters are shown by default
      var ellipsestext = "...";
      var moretext = "read more";
      var lesstext = "read less";
      
      $('.more').each(function() {
          var content = $(this).html();
   
          if(content.length > showChar) {
   
              var c = content.substr(0, showChar);
              var h = content.substr(showChar, content.length - showChar);
   
              // var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink text-info">' + moretext + '</a></span>';

              var html = c + '<a href="#" data-placement="bottom" data-toggle="tooltip" title="'+h+'" >' + ellipsestext+ '&nbsp;</a>';
   
              $(this).html(html);
          }
   
      });
   
      $(".morelink").click(function(){
          if($(this).hasClass("less")) {
              $(this).removeClass("less");
              $(this).html(moretext);
          } else {
              $(this).addClass("less");
              $(this).html(lesstext);
          }
          $(this).parent().prev().toggle();
          $(this).prev().toggle();
          return false;
      });

      // tooltip
      $('[data-toggle="tooltip"]').tooltip();
  }); 
</script>   
<style>
#submissionschart canvas{
  margin-left: 0px !important;
  margin-top: 0px !important;
}

</style>

@endsection

