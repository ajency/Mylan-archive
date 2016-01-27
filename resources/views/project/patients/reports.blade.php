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
                            <h4>Health Score Results compared with Previous Scores</h4>
                                 <p>The Table below shows the Health Scores for each Week and the Change in their Health when compared with previous Score & Baseline</p>
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
                             
                              <h4>Report on Weight of the Patient</h4>
                                 <p>The Table below shows the Weight of the Patient over the period. This information has been extracted 
                                    from answer to the Question "What is your current Weight" submitted by the Patient as part of
                                    Questionnaire "MRI Pancreatitis".</p>
                              <br><br>
                       
                        <select class="pull-right" name="generateChart">
                         
                          @foreach($inputLabels as $questionId => $label)
                            <option value="{{ $questionId }}">{{ $label }}</option>
                          @endforeach
                        </select> 

                           <div id="chartdiv" class="p-t-20"></div>
                        </div>
                     </div>
                     </div>

<?php 

$questionId = current(array_keys($inputLabels));
$inputJson = (isset($inputChartData[$questionId])) ? json_encode($inputChartData[$questionId]):'[]';
$inputLabel = (isset($inputLabels[$questionId]))?$inputLabels[$questionId]:'';
$maxScore =  (isset($allScore[$questionId]))?(max($allScore[$questionId]) + 10):10;
$baseLine = (isset($baseLineArr[$questionId]))?$baseLineArr[$questionId]:'';
?>
<script type="text/javascript">
 $(document).ready(function() {
 patientInputGraph(<?php echo $inputJson;?>,'{{$inputLabel}}',{{$maxScore}},{{$baseLine}},'chartdiv');

 $('select[name="generateChart"]').change(function (event) { 
      <?php 
      foreach($inputLabels as $questionId => $label)
      {
        $inputJson = (isset($inputChartData[$questionId])) ? json_encode($inputChartData[$questionId]):'[]';
        $inputLabel = (isset($inputLabels[$questionId]))?$inputLabels[$questionId]:'';
        $maxScore =  (isset($allScore[$questionId]))?(max($allScore[$questionId]) + 10):10;
        $baseLine = (isset($baseLineArr[$questionId]))?$baseLineArr[$questionId]:'';
        ?>
        if($(this).val()=='{{$questionId}}')
        { 
          patientInputGraph(<?php echo $inputJson;?>,'{{$inputLabel}}',{{$maxScore}},{{$baseLine}},'chartdiv');
        }

        <?php
      }
      ?>

    });

  });
</script>
@endsection
