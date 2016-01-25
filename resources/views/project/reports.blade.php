@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Users</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

 
<div class="page-title">
                     <h3><span class="semi-bold">Reports</span></h3>
       
                  </div>
                  <div class="grid simple">
                        <div class="grid-body no-border table-data">
                               <br>
       <div class="row">
         <div class="col-sm-8">
           
       </div>
       <div class="col-sm-4 m-t-10">
         <form name="searchData" method="GET"> 
               <select class="pull-right" name="referenceCode">
                   @foreach($allPatients as $patient)
                     <option value="{{ $patient }}">{{ $patient }}</option>
                   @endforeach
                  </select> 
         </form>
 
          </div>
      </div>
       <hr>
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
                                             @foreach($singleChoiceQuestion as $questionId => $question)
                                             <tr>
                                                <td>{{ $question }}</td>
                                                @foreach($responseArr as $responseId => $response)
                                                <?php
                                                if(isset($submissions[$responseId][$questionId]))
                                                {
                                                  $class='bg-'.$submissions[$responseId][$questionId]['baslineFlag'];
                                                  $flag= ($submissions[$responseId][$questionId]['previousFlag']=='no_colour')?'hidden':'text-'.$submissions[$responseId][$questionId]['previousFlag'];
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
                        <br><br>  

        <select class="pull-right" name="generateQuestionChart">
          @foreach($questionLabels as $questionId => $label)
            <option value="{{ $questionId }}">{{ $label }}</option>
          @endforeach
         </select> 

                           <div id="totalbaseline" class="p-t-20" style="width:100%; height:400px;"></div>
                
                   </div>
                     </div>
 <?php 

$questionId = current(array_keys($questionLabels));
$inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
$questionLabel = (isset($questionLabels[$questionId]))?$questionLabels[$questionId]:'';
$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:'';
 
?>

 <script type="text/javascript">
     

   $(document).ready(function() {
 
    //question chart
    patientInputGraph(<?php echo $inputJson;?>,'{{$questionLabel}}',0,{{$baseLine}},'totalbaseline');
 
 

     $('select[name="generateQuestionChart"]').change(function (event) { 
      <?php 
      foreach($questionLabels as $questionId => $questionLabel)
      {
        $inputJson = json_encode($questionChartData[$questionId]);
        $baseLine = $questionBaseLine[$questionId];
        ?>
        if($(this).val()=='{{$questionId}}')
        { 
          patientInputGraph(<?php echo $inputJson;?>,'{{$questionLabel}}',0,{{$baseLine}},'totalbaseline');
        }

        <?php
      }
      ?>

    });

     $('select[name="referenceCode"]').change(function (event) { 
         $('form').submit();
    });

});
 

    
</script>
@endsection