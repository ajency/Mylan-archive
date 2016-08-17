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
    <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Submission Notifications</a> </li>

  </ul>
</p>

<!-- END BREADCRUMBS -->
@endsection
@section('content')
<a class="btn btn-primary pull-right" id="btnSave" title="Download this page as a printable PDF"><i class="fa fa-print"></i> Get PDF
  <span class="addLoader"></span></a>
<div id="page1">    
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
              Submission Notifications Report
              <sm class="light">(These are the notifications generated for submissions)</sm>
            </h4>
            <div class="tools">
              <label class="filter-label">Filter</label>
              <form method="get">  
                <select name="reviewStatus" id="reviewStatus" class="pull-right select2 m-t-5 m-b-20 form-control inline filterby pull-right">
                  <option value="all">All</option>
                  <option {{ ($reviewStatus=='reviewed_no_action')?'selected':''}} value="reviewed_no_action">Reviewed - No action</option>
                  <option {{ ($reviewStatus=='reviewed_call_done')?'selected':''}} value="reviewed_call_done">Reviewed - Call done</option>
                  <option {{ ($reviewStatus=='reviewed_appointment_fixed')?'selected':''}} value="reviewed_appointment_fixed">Reviewed - Appointment fixed</option>
                  <option {{ ($reviewStatus=='unreviewed')?'selected':'' }} value="unreviewed">Unreviewed</option>
                  <!-- <option {{ ($reviewStatus=='missed')?'selected':'' }} value="missed">Missed</option> -->
                </select>
                <span class="cf-loader hidden m-t-3 submissionFilter"></span>
              </form>

            </div>
          </div>
          <div class="grid-body no-border" style="display: block;">
            <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
              <thead class="cf">
                <tr>
                  <th class="sorting "># Submission<br><br></th>

                  <th class="sorting" width="35%">Reason<br><br>
                  </th>
                  <th class="sorting" width="35%">Review Note<br><br>
                  </th>
                  <th class="sorting">Review Status<br><br>
                  </th>
                </tr>
              </thead>
              <tbody id="submissionData" limit="5" object-type="submission" object-id="0">

                @if(!empty($submissionNotifications['alertMsg']))
                <?php
                $firstBreak = 0;
                $firstBreakCapture = 0;
                $addClass = "";
                ?>    
                @foreach($submissionNotifications['alertMsg'] as $submissionNotification)
                <?php
// pdf
                $firstBreak = $firstBreak + 1;
                if($firstBreakCapture == 0){
                  if($firstBreak == 7){
                    $addClass = "printPdfMargin"; 
                    $firstBreakCapture = 1;
                    $firstBreak = 0;
                  }else{
                    $addClass = "";
                  }
                }else{
                  if($firstBreak == 9){
                    $addClass = "printPdfMarginE"; 
                    $firstBreak = 0;
                  }else{
                    $addClass = "";
                  }

                }
                ?>
                <tr class="<?php echo $addClass; ?>" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/{{$submissionNotification['URL']}}';">

                  <td class="text-center">
                    <h4 class="semi-bold m-0 flagcount">{{ $submissionNotification['occurrenceDate'] }}</h4>
                    <sm><b>#{{ $submissionNotification['sequenceNumber'] }}</b></sm>
                  </td>
                  <td class="text-center text-success">{{ sprintf($submissionNotification['msg'], $submissionNotification['previousTotalRedFlags'],$submissionNotification['sequenceNumber'] ) }}</td> 
                  <td class="text-center">{{ $submissionNotification['reviewNote'] }}</td>
                  <td class="text-center text-success">
                    <!-- <div class="submissionStatus" @if(strlen($submissionNotification['reviewStatus']) >10 ) data-toggle="tooltip" @endif data-placement="top" title="{{ getStatusName($submissionNotification['reviewStatus']) }}">{{ getStatusName($submissionNotification['reviewStatus']) }}</div> -->
                    <div class="submissionStatus" style="width: 100%;">{{ getStatusName($submissionNotification['reviewStatus']) }}</div>
                  </td>
                </tr>


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

    $(document).ready(function() {

      $('select[name="reviewStatus"]').change(function (event) { 
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
$(".table tr.printPdfMargin td").addClass("print-pdf-marginPSN");
$(".table tr.printPdfMarginE td").addClass("print-pdf-marginPSNE");
$(".addLoader").addClass("cf-loader");
$("#page1").css("background","#FFFFFF");

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
    doc.save( 'Patient Submission Notification.pdf');﻿
  }
});
setInterval(function(){ 
  $(".addLoader").removeClass("cf-loader"); 
  $(".table tr.printPdfMargin td").removeClass("print-pdf-marginPSN");
  $(".table tr.printPdfMarginE td").removeClass("print-pdf-marginPSNE");
  $("#page1").css("background","");
}, 3000);   
});
});  

</script>

@endsection
