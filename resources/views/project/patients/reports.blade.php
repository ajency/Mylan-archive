@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
         <li><a href="#">Patients</a></li>
        <li><a href="#">{{ $patient['reference_code']}}</a> </li>
        <li><a href="#" class="active">Reports</a> </li>
     
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div class="pull-right">
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
<div class="page-title">
     <h3>Report of Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
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
                             <br><br>
                          <div class="tableOuter">
                        <div class="x-axis-text">Submissions</div>
                       <div class="y-axis-text">Questions</div>
                       <div class="table-responsive sticky-table-outer-div {{(count($responseArr)>10)?'sticky-tableWidth':''}}"> 
                           <table class="table">
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
                                       </table>
                       </div>   
                       </div>
                           
                            
                                       <br>
                                    <hr>
                  
                                       <br>
                             
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

                               <div id="questionChart" class="p-t-20" style="width:100%; height:400px;"></div>

                                             <hr>
                             
                              <h4 class="bold">Question score per submission graph</h4>
                                 <p>The graph displays previous score,current score and the baseline score of a patient for every question for the selected submission</p>
                              <br><br>
                       <label class="pull-right">
                          Choose Submissions
                          <br>
                         <select class="pull-right" name="generateSubmissionChart">
                          @foreach($submissionNumbers as $submissionNumber => $responseId)
                          <option value="{{ $responseId }}">Submission {{ $submissionNumber }}</option>
                          @endforeach
                        </select> 
                      </label>
                

                       <div id="submissionChart" class="p-t-20" style="width:100%; height:500px;"></div>

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

    });

  });
</script>
 
@endsection
