@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
  <ul class="breadcrumb">
    <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
    <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
    <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" class="ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
    <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Submissions</a> </li>

  </ul>
</p>

<!-- END BREADCRUMBS -->
@endsection
@section('content')
<a class="btn btn-primary pull-right" id="btnSave" title="Download this page as a printable PDF"><i class="fa fa-print"></i> Get PDF
  <span class="addLoader"></span></a>
<div id="page1"> 
  <div class="pull-right m-r-15">
    <a href="add-patient.html" class="hidden btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Patient</a>
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
  <div class="m-r-15 pull-right patient-search">
    <select class="selectpicker" data-live-search="true" title="Patient" name="referenceCode">
      <option class="ttuc" value="">-select patient-</option>
      @foreach($allPatients as $patientData)
      <option class="ttuc patient-refer{{ $patientData['reference_code'] }}" {{($patient['reference_code']==$patientData['reference_code'])?'selected':''}}  value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
      @endforeach
    </select> 
  </div> 
  <div class="page-title">
    <h3>Patient <span class="semi-bold ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>
  </div>
  <div class="tabbable tabs-left">
    @include('project.patients.side-menu')
    <div class="tab-content">
      <div class="tab-pane table-data" id="Patients">
      </div>
      <div class="tab-pane table-data active" id="Submissions">
        <div class="grid simple grid-table">
          <div class="grid-title no-border">
            <h4>
              Submissions <span class="semi-bold">Summary</span> 
              <sm class="light">(These are scores & flags for current submissions)</sm>
            </h4>
            <div class="tools">
              <label class="filter-label">Filter</label>
              <form method="get">  
                <select name="submissionStatus" id="submissionStatus" class=" select2  form-control inline filterby pull-left -m-5">
                  <option value="all">All</option>
                  <option {{ ($submissionStatus=='completed')?'selected':'' }} value="completed">Completed</option>
                  <option {{ ($submissionStatus=='late')?'selected':'' }} value="late">Late</option>
                  <option {{ ($submissionStatus=='missed')?'selected':'' }} value="missed">Missed</option>
                </select>
                <span class="cf-loader hidden m-t-3 submissionFilter"></span>
              </form>

            </div>
          </div>
          <div class="grid-body no-border" style="display: block;">
            <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="status" cond="{{ $submissionStatus }}">
              <thead class="cf">
                <tr>
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
              <tbody id="submissionData" limit="" object-type="patient-submission" object-id="{{ $patient['reference_code']}}">
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
// pdf
                $firstBreak = $firstBreak +1;
                if($firstBreakCapture == 0){
                  if($firstBreak == 9){
                    $addClass = "printPdfMargin"; 
                    $firstBreakCapture = 1;
                    $firstBreak = 0;
                  }else{
                    $addClass = "";
                  }
                }else{
                  if($firstBreak == 12){
                    $addClass = "printPdfMarginE"; 
                    $firstBreak = 0;
                  }else{
                    $addClass = "";
                  }

                }
                ?>
                @if($submission['status']=='missed' || $submission['status']=='late')
                <tr class="<?php echo $addClass; ?>">
                  <td>
                    <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                    <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                  </td>

                  <td class="text-right sorting">0</td>
                  <td class="text-center sorting">0</td>
                  <td class="text-left sorting">0</td>

                  <td class="text-right semi-bold margin-none flagcount p-h-0">
                    <h4>
                      -
                    </h4>
                  </td>
                  <td class="text-center semi-bold margin-none flagcount p-h-0">
                    <h4>
                      <b>/</b>
                    </h4>
                  </td>
                  <td class="text-left semi-bold margin-none flagcount p-h-0">
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
                  <td>
                    <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                    <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                  </td>

                  <td class="text-right sorting">{{ $submission['baseLineScore'] }}</td>
                  <td class="text-center sorting">{{ $submission['previousScore'] }}</td>
                  <td class="text-left sorting">{{ $submission['totalScore'] }}</td>

                  <td class="text-right semi-bold margin-none flagcount p-h-0">
                    <h4><b class="text-{{ $submission['totalBaseLineFlag'] }}">{{ $submission['comparedToBaslineScore'] }}</b></h4>
                  </td>  
                  <td class="text-center semi-bold margin-none flagcount p-h-0">
                    <h4><b>/</b></h4>                                      
                  </td>
                  <td class="text-left semi-bold margin-none flagcount p-h-0">
                    <h4> <b class="f-w text-{{ $submission['totalPreviousFlag'] }}">{{ $submission['comparedToPrevious'] }}</b></h4>
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
                    <div class="submissionStatus more">{{ getStatusName($submission['reviewed']) }} {{ ($project[$submission['reviewed']])?$project[$submission['reviewed']]:'' }}</div>
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
        </div>

      </div>
      <div class="tab-pane " id="Reports">

      </div> 
    </div>
  </div>
</div>  

  <script type="text/javascript">
    var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
    var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 


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


//pdf
$(function() { 
  $("#btnSave").click(function() { 
//convert all svg's to canvas
$(".table tr.printPdfMargin td").addClass("print-pdf-marginPat-S");
$(".table tr.printPdfMarginE td").addClass("print-pdf-marginPat-SE");
$("#page1").css("background","#FFFFFF");
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
    doc.save( 'Patient Submissions.pdf');﻿
  }
});
setInterval(function(){ 
  $(".addLoader").removeClass("cf-loader"); 
  $(".table tr.printPdfMargin td").removeClass("print-pdf-marginPat-S");
  $(".table tr.printPdfMarginE td").removeClass("print-pdf-marginPat-SE");
  $("#page1").css("background","");
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
            var h = content/*.substr(showChar, content.length - showChar)*/;
 
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

@endsection
