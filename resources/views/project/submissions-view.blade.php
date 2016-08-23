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
    <li>
      <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Submission</a>
    </li>
  </ul>
</p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<a class="btn btn-primary pull-right" id="btnSave" title="Download this page as a printable PDF"><i class="fa fa-print"></i> Get PDF
  <span class="addLoader"></span></a> 
  <div id="page1">  
    <div class="page-title">
      <h3>Patient <span class="semi-bold ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>

      <div class="patient-search pull-right">
        <form name="searchData" method="GET"> 
          <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
            <option class="ttuc" value="">-select patient-</option>
            @foreach($allPatients as $patientData)
            <option class="ttuc patient-refer{{ $patientData['reference_code'] }}" {{($patient['reference_code']==$patientData['reference_code'])?'selected':''}} value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
            @endforeach
          </select> 
        </form>
      </div>
    </div>
    <div class="tabbable tabs-left">
      @include('project.patients.side-menu')
      <div class="tab-content">
        <div class="tab-pane table-data" id="Patients">
        </div>
        <div class="tab-pane table-data active" id="Submissions">
          <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/submissions') }}"><i class="fa fa-caret-square-o-left"></i> Back to list of submissions</a>
          <h4><span class="semi-bold">{{ $questionnaire }}</span></h4>
          <!-- <p>(Showing Submission details)</p> -->
          <br>
          <div id="chartdiv" style="width:100%; Height:500px;"></div>
          <br>
          @if(!empty($inputValueChart))
          <table class="table table-flip-scroll cf table-center f-s-b">
            <thead class="cf">
              <tr>
                <th  class="text-left"></th>
                @foreach($inputValueChart as $inputValue)
                <th class="text-center">{{ $inputValue['question'] }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              <tr>
                <td  class="text-left"><i class="fa fa-circle green-current"></i> Current</td>
                @foreach($inputValueChart as $inputValue)
                <td class="bg-gray">{{ $inputValue['current'] }}</td>
                @endforeach
              </tr>
              <tr>
                <td  class="text-left"> <i class="fa fa-circle blue-previous"></i> Previous</td>
                @foreach($inputValueChart as $inputValue)
                <td class="bg-gray">{{ $inputValue['prev'] }}</td>
                @endforeach
              </tr>
              <tr>
                <td  class="text-left"><i class="fa fa-circle yellow-baseline"></i> Baseline</td>
                @foreach($inputValueChart as $inputValue)
                <td class="bg-gray">{{ $inputValue['base'] }}</td>
                @endforeach
              </tr>
            </tbody>
          </table>
          @endif
<!--                  <div class="row">
@foreach($inputValueChart as $inputValue)
<div class="submission-chart-wt text-center">
<div class="input-text-outer">
<span class="input-text">{{ $inputValue['question'] }}</span>
</div>
<div class="input-values p-b-10">  
<span class="previous {{ ($responseData['previousFlag']=='')?'hidden':'' }}">{{ $inputValue['prev'] }}</span>
<span class="baseline">{{ $inputValue['base'] }}</span>
<span class="current">{{ $inputValue['current'] }}</span>
</div>
</div>
@endforeach
</div> -->
<br> <br>
<div class="user-description-box">
  <div class="row">

  <!-- test -->
  <div class="col-md-9">
    <div class="row">
          <div class="col-md-6">

            <div class="row">

              <div class="col-md-12">
                <label>Submission Number</label>
                <select name="patientSubmission" id="patientSubmission" class="select2 form-control"  >
                  @foreach($allSubmissions as $responseId =>$submission)
                  <option value="{{$responseId}}" {{ ($currentSubmission==$responseId)?'selected':'' }}>{{ $submission }}</option>
                  @endforeach
                </select>
              </div>
            </div>

          </div>
          <div class="col-md-6">
            @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
            <div class="row">


              <div class="col-md-12 reviewStatus"> 
                <label>Review Status</label>
                <select name="updateSubmissionStatus" id="updateSubmissionStatus" class="select2 form-control full-width" object-id="{{ $currentSubmission }}">            
                  <option {{ ($responseData['reviewed']=='reviewed_no_action')?'selected':''}} value="reviewed_no_action">Reviewed - No action</option>
                  <option {{ ($responseData['reviewed']=='reviewed_call_done')?'selected':''}} value="reviewed_call_done">Reviewed - Call done</option>
                  <option {{ ($responseData['reviewed']=='reviewed_appointment_fixed')?'selected':''}} value="reviewed_appointment_fixed">Reviewed - Appointment fixed</option>
                  <option {{ ($responseData['reviewed']=='unreviewed')?'selected':''}} value="unreviewed" >Unreviewed</option>
                </select>

      <!--  <div class="notes">
      <i class="fa fa-sticky-note" data-toggle="tooltip" data-placement="top" title="{{ $responseData['reviewNote'] }}"></i>
      </div> -->

      </div>

      <!-- <div class="col-md-2 m-t-15 hidden"> <span class="cf-loader"></span></div> -->

      </div>
      @endif
      </div>
    </div>

    <div class="row m-t-30">
      <div class="col-md-6">
        <div><label>Submitted on {{ $submittedDate }}</label></div>
      </div>
      <div class="col-md-6">
        @if($responseData['reviewNote']!='')

        <div class="Notes">
          <label class="fln">Reviewed on {{ $updatedDate }}</label>
          <label class="fln">Notes: {{ $responseData['reviewNote'] }}</label> 
        </div>

        @endif

      </div>
    </div>

  </div>
  
  <!-- /test -->


<!--div class="col-md-3 baselineAllign text-right ">
<div class="pull-right">
Previous <span class="p-l-r-5">|</span> Baseline
</div>
<br>
<div class="pull-right flagsAllignment">
<span class="p-l-r-5 text-{{ $responseData['previousFlag'] }} {{ ($responseData['previousFlag']=='')?'hidden':'' }}"><i class="fa fa-flag"></i></span><span class="text-muted p-l-r-5">|</span>
<span class="text-{{ $responseData['baseLineFlag'] }}"><i class="fa fa-flag"></i></span>
</div>


</div-->
<div class="col-md-3 baselineAllign text-right" style="margin-top:-15px;">
  <div align="center"><label class="fw600 fosz14">Total Score</label></div>
  <table style="width: 100%;">
    <tbody>
      <tr class="lh18">
        <td class="t-center fw600">{{ $newTotalCounterArr['current'] }}<span class="p-l-r-5"></span></td>
        <td class="t-center fw600">{{ $newTotalCounterArr['prev'] }}<span class="p-l-r-5"></span></td>
        <td class="t-center fw600">{{  $newTotalCounterArr['base'] }}</td>
      </tr>
      <tr class="lh18">
        <td>Current<span class="p-l-r-5"></span></td>
        <td>Previous<span class="p-l-r-5"></span></td>
        <td>Baseline</td>
      </tr>
      <!-- <tr>
        <td>&nbsp;</td>
        <td class="t-center"><span class="p-l-r-5 text-{{ $responseData['previousFlag'] }} {{ ($responseData['previousFlag']=='')?'hidden':'' }}"><i class="fa fa-flag"></i></span></td>
        <td class="t-center"><span class="text-{{ $responseData['baseLineFlag'] }}"><i class="fa fa-flag"></i></span></td>
      </tr> -->
    </tbody>
  </table>

  <div class="bb"></div>

  <div class="row text-center">
    <div class="col-sm-6">
      <p class="m-b-0 lh18">
        <i class="fa fa-flag text-red"></i>
      </p>
      <p class="fw600 fosz14 lh18">
        Baseline vs Curent
      </p>
    </div>
    <div class="col-sm-6">
      <p class="m-b-0 lh18">
        <i class="fa fa-flag text-red"></i>
      </p>
      <p class="fw600 fosz14 lh18">
        Previous vs Current
      </p>
    </div>
  </div>
</div>

</div>




</div>
<br>

</div>
<br>

<div class="user-description-box">
  <?php 
  $i=1;
  $firstBreak = 0;
  $firstBreakCapture = 0;
  $addClass = "";  
  ?>
  @foreach($answersList as $answer)
  <?php
// pdf
  $firstBreak = $firstBreak +1;
  if($firstBreakCapture == 0){
    if($firstBreak == 2){
      $addClass = "printPdfMargin"; 
      $firstBreakCapture = 1;
      $firstBreak = 0;
    }else{
      $addClass = "";
    }
  }else{
    if($firstBreak == 4){
      $addClass = "printPdfMarginE"; 
      $firstBreak = 0;
    }else{
      $addClass = "";
    }

  }
  ?>
  <div class="grid simple <?php echo $addClass; ?>">
    <div class="grid-body">
      @if($answer['questionType']=='single-choice')
      <div class="pull-right questStats">
        {{ $answer['comparedToPrevious'] }}
        @if($answer['previousFlag']=='green')
        <span class="text-success"><i class="fa fa-flag"></i></span>
        @elseif($answer['previousFlag']=='red')
        <span class="text-danger"><i class="fa fa-flag"></i></span>
        @elseif($answer['previousFlag']=='amber')
        <span class="text-warning"><i class="fa fa-flag"></i></span>
        @endif

        <span class="text-muted p-l-r-5">|</span>

        {{ $answer['comparedToBaseLine'] }}
        @if($answer['baseLineFlag']=='green')
        <span class="text-success p-l-5"><i class="fa fa-flag"></i></span>
        @elseif($answer['baseLineFlag']=='red')
        <span class="text-danger p-l-5"><i class="fa fa-flag"></i></span>
        @elseif($answer['baseLineFlag']=='amber')
        <span class="text-warning p-l-5"><i class="fa fa-flag"></i></span>
        @endif

      </div>
      @endif
      <label class="semi-bold">Q {{$i}} ) {{ $answer['question']}}</label>
      @if($answer['questionType']=='multi-choice')
      <?php
      $x = 'A';
      ?>
      @foreach($answer['option'] as $option)
      <h5 class="text-success semi-bold">{{ $x }}: {{ $option }}</h5>
      <?php $x++;?>
      @endforeach
      @elseif($answer['questionType']=='input')

      <span class="text-success"><b>{{ getInputValues($answer['optionValues']) }}</b> </span>

      @else
      <h5 class="text-success semi-bold">A: {{ $answer['value']}} {{ $answer['option']}}</h5>
      @endif


      @if(isset($previousAnswersList[$answer['questionId']]))
      <h5 class="text-success g-l-h"><span class="text-muted">Previous Answer:</span>

        @if($previousAnswersList[$answer['questionId']]['questionType']=='multi-choice')
        <?php
        $x = 'A';
        ?>
        <br>
        @foreach($previousAnswersList[$answer['questionId']]['option'] as $option)
        <span class="text-info"><b>{{ $x }}</b>: {{ $option }}</span>  <br>
        <?php $x++;?>
        @endforeach


        @elseif($previousAnswersList[$answer['questionId']]['questionType']=='input')

<!-- @foreach($previousAnswersList[$answer['questionId']]['optionValues'] as $optionLabel=> $optionValue)
<span class="text-info"><b>{{ $optionValue }} {{ $optionLabel }}</b> </span>   

@endforeach -->
<span class="text-info"><b>{{ getInputValues($previousAnswersList[$answer['questionId']]['optionValues']) }}</b> </span>

@else
<span class="text-info">{{ $previousAnswersList[$answer['questionId']]['value']}} {{ $previousAnswersList[$answer['questionId']]['option']}}</span>
@endif
</h5>
@endif
@if(isset($baseLineAnswersList[$answer['questionId']]))
<h5 class="text-success g-l-h"><span class="text-muted">Base Line Answer:</span>

  @if($baseLineAnswersList[$answer['questionId']]['questionType']=='multi-choice')
  <?php
  $x = 'A';
  ?>
  <br>
  @foreach($baseLineAnswersList[$answer['questionId']]['option'] as $option)
  <span class="text-info"><b>{{ $x }}</b>: {{ $option }}</span>  <br>
  <?php $x++;?>
  @endforeach
  @elseif($baseLineAnswersList[$answer['questionId']]['questionType']=='input')


  <span class="text-info"><b>{{ getInputValues($baseLineAnswersList[$answer['questionId']]['optionValues']) }} </b></span>   




  @else
  <span class="text-info">{{ $baseLineAnswersList[$answer['questionId']]['value']}} {{ $baseLineAnswersList[$answer['questionId']]['option']}}</span>
  @endif
</h5>
@endif
</div>
</div>
<?php $i++;?>
@endforeach

</div>
</div>
<div class="tab-pane " id="Reports">

</div> 
</div>
</div>

<!-- Modal Code -->

<div class="modal fade customModal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="notesForm" method="post" action="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $currentSubmission }}/updatesubmissionstatus" data-parsley-validate>
        <div class="modal-header text-left">
          <h3>Notes</h3>
        </div>
        <div class="modal-body validError">
          <textarea name="reviewNote" data-parsley-required></textarea>
        </div>  
        <div class="notes-err-msg" style="display:none;">This value is required</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default closeModel" >Cancel</button>
          <button type="submit" class="btn btn-primary">Submit</button> <span class="cf-loader hidden m-t-35" id="statusLoader"></span>
        </div>
        <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
        <input type="hidden" value="{{ $responseData['reviewed'] }}" name="updateSubmissionStatus"/>
        <input type="hidden" value="{{ $responseData['reviewed'] }}" name="oldStatus"/>

      </form>

    </div>
  </div>
</div>





<script type="text/javascript">


  $(document).ready(function() {

    submissionBarChart(<?php echo $submissionJson; ?>,'chartdiv');

    $('select[name="patientSubmission"]').change(function (event) { 
      window.location="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/"+$(this).val();
    });

    $('.closeModel').click(function (event) { 
      var oldStatus = $(this).closest('form').find('input[name="oldStatus"]').val();
      $('select[name="updateSubmissionStatus"]').val(oldStatus);
      $('textarea[name="reviewNote"]').val('');
      $('#myModal').modal('hide');
    });

    $('select[name="referenceCode"]').change(function (event) { 
      var referenceCode = $(this).val();
      if(referenceCode!='')
        window.location.href = BASEURL+"/patients/"+referenceCode; 
    });

    $("#notesForm").on(submit(function(event) {
      alert(1);
    });

  });
  
//pdf
$(function() { 
  $("#btnSave").click(function() { 
//convert all svg's to canvas
$(".user-description-box div.grid.simple.printPdfMargin div").attr("style","padding-bottom: 70px !important");
$(".user-description-box div.grid.simple.printPdfMarginE div").attr("style","padding-bottom: 72px !important");
$(".addLoader").addClass("cf-loader");
$("#page1").css("background","#FFFFFF");
$(".f-s-b").css("margin-top","130px");

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
    doc.save( 'Patient Submission.pdf');﻿
  }
});
setInterval(function(){ 
  $(".addLoader").removeClass("cf-loader"); 
  $(".user-description-box div.grid.simple.printPdfMargin div").attr("style","");
  $(".user-description-box div.grid.simple.printPdfMarginE div").attr("style","");
  $("#page1").css("background","");
  $(".f-s-b").css("padding-top","0px");

}, 3000);   
});
}); 
</script> 

<style>

  .validError .parsley-errors-list.filled{
    display: none !important;
  }
  .validError .parsley-error{
    border: 1px solid #f35958!important;
  }  
  .t-center{
    text-align: center;
  }
  .notes-err-msg{
    color: #f35958;
    padding-left:15px;
  }
</style>


@endsection