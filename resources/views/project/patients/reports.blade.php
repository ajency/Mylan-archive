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
    <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Reports</a> </li>


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
    <h3>Report of Patient <span class="semi-bold ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>
  </div>
  <div class="tabbable tabs-left">
    @include('project.patients.side-menu')
    <div class="tab-content">
      <div class="tab-pane table-data" id="Patients">
      </div>
      <div class="tab-pane" id="Submissions">

      </div>
      <div class="tab-pane active" id="Reports">
        <h4 class="bold">Patient health chart</h4>
        <p>Patient health chart shows the comparison between
          <br>1.Patients current score to baseline score- indicated by highlighted cell
          <br>2.Patients current score to previous score indicated by flag
          Red-indicates current score is worse then previous/baseline score by 2 points
          Amber-indicates current score is worse then previous/baseline score by 1 point
          Green-indicates current score is better then previous/baseline score
          White-Indicates current score is same as baseline score.
          Flag is not displayed if the current score is same as previous score</p>

          <div class="tableOuter">
            <div class="compared-to">
              <span><i class="fa fa-stop"></i> Compared to Baseline</span> 

              <span><i class="fa fa-flag"></i> Compared to Previous</span>               
            </div>
            <br>
            <div class="x-axis-text">Submissions</div>
            <div class="y-axis-text">Questions</div>
            <div class="table-responsive {{(!empty($responseArr))?'sticky-table-outer-div':''}} {{(count($responseArr)>10)?'sticky-tableWidth':''}}"> 
              <table class="table">
                @if(empty($responseArr))
                <tbody>
                  <tr><td class="no-data-found"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                </tbody>
                @else
                <thead class="cf">
                  <tr>
                    <th class="headcol th-headcol"></th>
                    @foreach($responseArr as $response)
                    <th>{{ $response['DATE'] }} ({{ $response['SUBMISSIONNO'] }})</th>
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                  @foreach($questionArr as $questionId => $question)
                  <tr>
                    <td class="headcol">{{ $question }}</td>
                    @foreach($responseArr as $responseId => $response)
                    <?php
                    if(isset($submissionArr[$responseId][$questionId]))
                    {
                      $class='bg-'.$submissionArr[$responseId][$questionId]['baslineFlag'];
                      $flag= ($submissionArr[$responseId][$questionId]['previousFlag']=='no_colour'|| $submissionArr[$responseId][$questionId]['previousFlag']=='')?'hidden':'text-'.$submissionArr[$responseId][$questionId]['previousFlag'];
                    }
                    else
                    {
                      $class='bg-gray';
                      $flag = 'hidden';
                    }
                    ?>
                    <td class="{{ $class }}"><i class="fa fa-flag {{ $flag }}" ></i></td>

                    @endforeach

                  </tr>
                  @endforeach

                </tbody>
                @endif
              </table>
            </div>   
          </div>


          <br>
          <hr>

          <br>
          <div class="q-s-c"></div>
          <h4 class="bold">
            Question score chart</h4>
            <p>Question score chart shows the score of each question with reference to baseline for all submissions</p>
            <br><br>
            <label class="pull-right">
              Choose Questions
              <br>
              <select name="generateQuestionChart">
                @foreach($questionLabels as $questionId => $label)
                <option value="{{ $questionId }}">{{ $label }}</option>
                @endforeach
              </select>
            </label> 
            @if(!$totalResponses)
            <table class="table table-flip-scroll table-hover dashboard-tbl">
              <tbody>
                <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
              </tbody>
            </table>
            @else
            <div id="questionChart" class="p-t-20" style="width:100%; height:400px;"></div>
            @endif
            <hr>

            <h4 class="bold print-pdf-padding">Question score per submission graph</h4>
            <p>The graph displays previous score,current score and the baseline score of a patient for every question for the selected submission</p>
            <br><br>

            <div class="row">
                <div class="col-sm-12">
                <div class="clearfix">
                  <div class="pull-left">
                  <table id="weight-table" class="table table-flip-scroll cf table-center f-s-b border-none m-r-20" style="margin-top: -20px;">
                      <thead class="cf">
                        <tr>
                          <th class="text-left" width="120px"></th>
                          <th class="text-left" width="120px"></th>

                          <th width="120px"></th>
                          <th width="120px"></th>
                        </tr>
                      </thead>
                      @if($inputValueChart)
                          <?php $weightCt = 0;?>
                          @foreach($inputValueChart as $inputValueChartK => $inputValueChartV)
                            <?php 
                                $styleWeight = "hide" ;
                                if($weightCt == 0){
                                  $styleWeight = "show";
                                  $weightCt = 1;
                                }
                            ?>
                      <tbody class="hide-{{ $inputValueChartK }} <?php echo $styleWeight; ?>">
                        <tr>
                          <td> &nbsp;</td>
                          <td class="text-center"><i class="fa fa-circle green-current"></i> Current</td>
                          <td class="text-center"> <i class="fa fa-circle blue-previous"></i> Previous</td>
                          <td class="text-center"><i class="fa fa-circle yellow-baseline"></i> Baseline</td>

                        </tr>
                          <tr >

                            <td class="semi-bold">Weight</td>

                            <td class="bg-gray">{{ $inputValueChart[$inputValueChartK]['current'] }}</td>

                            <td class="bg-gray">{{ $inputValueChart[$inputValueChartK]['prev'] }}</td>

                            <td class="bg-gray">{{ $inputValueChart[$inputValueChartK]['base'] }}</td>
                          </tr>
                      </tbody>
                       @endforeach
                        @else
                        <tbody>
                        <tr>
                          <td> &nbsp;</td>
                          <td class="text-center"><i class="fa fa-circle green-current"></i> Current</td>
                          <td class="text-center"> <i class="fa fa-circle blue-previous"></i> Previous</td>
                          <td class="text-center"><i class="fa fa-circle yellow-baseline"></i> Baseline</td>

                        </tr>
                          <tr >

                            <td class="semi-bold">Weight</td>

                            <td class="bg-gray">-</td>

                            <td class="bg-gray">-</td>

                            <td class="bg-gray">-</td>
                          </tr>
                        </tbody>
                        @endif
                    </table>
                  </div>

                        <select class="pull-right m-b-10" name="generateSubmissionChart">
                          @foreach($submissionNumbers as $submissionNumber => $responseId)
                          <option value="{{ $responseId }}">Submission {{ $submissionNumber }}</option>
                          @endforeach
                        </select>
                      </div>
                        
                        @if(!$totalResponses)
                        <table class="table table-flip-scroll table-hover dashboard-tbl">
                          <tbody>
                            <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                          </tbody>
                        </table>
                        @else
                        <div id="submissionChart" class="p-t-20" style="width:100%; height:500px;"></div>
                        @endif
                      </div>
                    </div>


            <label class="pull-right">
              Choose Submissions
              <br>
              <select class="pull-right" name="generateSubmissionChart">
                @foreach($submissionNumbers as $submissionNumber => $responseId)
                <option value="{{ $responseId }}">Submission {{ $submissionNumber }}</option>
                @endforeach
              </select> 
            </label>

            @if(!$totalResponses)
            <table class="table table-flip-scroll table-hover dashboard-tbl">
              <tbody>
                <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
              </tbody>
            </table>
            @else
            <div id="submissionChart" class="p-t-20" style="width:100%; height:500px;"></div>
            @endif      
          </div>
        </div>
      </div>
</div>      

      <?php 

      $questionId = current(array_keys($questionLabels));
      $inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
      $questionLabel = (isset($questionLabels[$questionId]))?$questionLabels[$questionId]:'';
// $baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;


      $submissionJson = (isset($submissionChart[$firstSubmission])) ? json_encode($submissionChart[$firstSubmission]):'[]';
      ?>

      <script type="text/javascript">

        var STARTDATE = '{{ date("D M d Y", strtotime($startDate)) }}'; 
        var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }}'; 

        $(document).ready(function() {

// Always scroll to right 
$('.sticky-table-outer-div').animate({scrollLeft: 99999}, 300);

shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$questionLabel}}',0,'questionChart','Submissions','Score');

//submission chart
submissionBarChart(<?php echo $submissionJson; ?>,'submissionChart');

$('select[name="generateQuestionChart"]').change(function (event) { 
  <?php 
  foreach($questionLabels as $questionId => $label)
  {
    $inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
// $baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;
    ?>
    if($(this).val()=='{{$questionId}}')
    { 
      shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$label}}',0,'questionChart','Submissions','Score');
    }

    <?php
  }
  ?>

});

$('select[name="generateSubmissionChart"]').change(function (event) { 
  <?php 
  foreach($submissionNumbers as $submissionNumber => $responseId)
  {
    $submissionJson = json_encode($submissionChart[$responseId]);
    ?>
    if($(this).val()=='{{$responseId}}')
    { 
      submissionBarChart(<?php echo $submissionJson; ?>,'submissionChart');
    }

    <?php
  }
  ?>

  $("#weight-table tbody").removeClass("show");
  $("#weight-table tbody").css("border-top","0px solid");
  $("#weight-table tbody").addClass("hide");
  $("#weight-table tbody.hide-"+$('select[name="generateSubmissionChart"]').val()).addClass("show");

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

$(".addLoader").addClass("cf-loader");
$("#page1").css("background","#FFFFFF");
/*$(".q-s-c").css("padding-top","220px");
$(".print-pdf-padding").css("padding-top","280px");*/


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
        var doc = new jsPDF('p', 'mm', "a4");
        var position = 0;
        doc.addImage(imgData, 'JPEG', 10, 10, 200, 290);
        doc.save( 'Reports.pdf');﻿
     }
  });
/*html2canvas($("#page1"), {
  background: '#FFFFFF',
  onrendered: function(canvas) {
    var imgData = canvas.toDataURL("image/jpeg", 1.0);  
    var imgWidth = 290; 
    var pageHeight = 225;  
    var imgHeight = canvas.height * imgWidth / canvas.width;
    var heightLeft = imgHeight;

    var doc = new jsPDF('l', 'mm');
    var position = 0;

    doc.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    while (heightLeft >= 0) {
      position = heightLeft - imgHeight;
      doc.addPage();
      doc.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
      heightLeft -= pageHeight;
    }
    doc.save( 'Patient Reports.pdf');﻿
  }
});*/

setInterval(function(){ 
  $(".addLoader").removeClass("cf-loader"); 
  /*$(".q-s-c").css("padding-top","0px");
  $(".print-pdf-padding").css("padding-top","0px");*/

}, 3000);   
});
}); 
</script>

@endsection
