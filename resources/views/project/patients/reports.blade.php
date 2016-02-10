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
         <li>
            <a href="#"> Reports</a>
         </li> 
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
                          <div class="table-responsive"> 
                           <table class="table">
                                          <thead class="cf">
                                             <tr>
                                                <th>Week</th>
                                                @foreach($responseArr as $response)
                                                <th>{{ $response }}</th>
                                                @endforeach
                                             </tr>
                                          </thead>
                                          <tbody>
                                             @foreach($questionArr as $questionId => $question)
                                             <tr>
                                                <td>{{ $question }}</td>
                                                @foreach($responseArr as $responseId => $response)
                                                <?php
                                                if(isset($submissionArr[$responseId][$questionId]))
                                                {
                                                  $class='bg-'.$submissionArr[$responseId][$questionId]['baslineFlag'];
                                                  $flag= ($submissionArr[$responseId][$questionId]['previousFlag']=='no_colour')?'hidden':'text-'.$submissionArr[$responseId][$questionId]['previousFlag'];
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
                           
                            
                                       <br>
                                    <hr>
                  
                                       <br>
                             
                              <h4 class="bold">
Question score chart</h4>
                                 <p>Question score chart shows the score of each question with reference to baseline for all submissions</p>
                              <br><br>
                              <select class="pull-right" name="generateQuestionChart">
                                @foreach($questionLabels as $questionId => $label)
                                <option value="{{ $questionId }}">{{ $label }}</option>
                                @endforeach
                              </select> 

                               <div id="questionChart" class="p-t-20" style="width:100%; height:400px;"></div>

                                             <hr>
                             
                              <h4 class="bold">Question score per submission graph</h4>
                                 <p>The graph displays previous score,current score and the baseline score of a patient for every question for the selected submission</p>
                              <br><br>
                       
                        <select class="pull-right">
                                      <option value="volvo">Submission 1</option>
                                      <option value="saab">Submission 2</option>
                                       <option value="saab">Submission 3</option>
                                       <option value="saab">Submission 4</option>
                                       <option value="saab">Submission 5</option>
                                       <option value="saab">Submission 6</option>
                                          </select> 

                                           <div id="submissionrange" class="p-t-20"></div>
                        </div>
                     </div>
                     </div>

 <?php 

$questionId = current(array_keys($questionLabels));
$inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
$questionLabel = (isset($questionLabels[$questionId]))?$questionLabels[$questionId]:'';
$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;
?>

<script type="text/javascript">

  var STARTDATE = '{{ date("D M d Y", strtotime($startDate)) }}'; 
  var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }}'; 

 $(document).ready(function() {
 
 shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$questionLabel}}',{{$baseLine}},'questionChart')

 $('select[name="generateQuestionChart"]').change(function (event) { 
      <?php 
      foreach($questionLabels as $questionId => $label)
      {
        $inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
        $baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;
        ?>
        if($(this).val()=='{{$questionId}}')
        { 
          shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$label}}',{{$baseLine}},'questionChart')
        }

        <?php
      }
      ?>

    });

  });
</script>
@endsection
