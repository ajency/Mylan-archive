@extends('layouts.single-hospital')
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
                  <div class="grid simple">
                     <div class="grid-body">
                              <h4>Health Score Results compared with Previous Scores</h4>
                                 <p>The Table below shows the Health Scores for each Week and the Change in their Health when compared with previous Score</p>
                             <br><br>
                             <div class="row">
                                 <div class="col-sm-6">
                                 <div class="row"> 
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>Enrollment Date</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>Date of Birth</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                                 </div>
                                 <div class="col-sm-4 m-t-25">
                                    <a href="" class="btn btn-default">Apply</a>
                                 </div>
                              </div>
                              </div>
                              <div class="col-sm-6 m-t-25 text-right">
                              <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a>
                              <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download PDF</a>
                              </div>
                              </div>
                           <br>
                           <br>
                           <table class="table table-flip-scroll cf">
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
                                                <?php 

                                                $lastSubmittedScore =$baseLineArr[$questionId];
                                                ?>
                                                @foreach($responseArr as $responseId => $response)
                                                <?php
                                                $myscore ='';
                                                
                                                if(isset($submissionArr[$responseId][$questionId]))
                                                {
                                                  $myscore = $submissionArr[$responseId][$questionId];
                                                  
                                                  if($lastSubmittedScore < $myscore)
                                                    $class='bg-danger';
                                                  elseif($lastSubmittedScore > $myscore)
                                                    $class='bg-success';
                                                  else
                                                    $class='bg-warning';
                                                }
                                                else
                                                {
                                                  $class='bg-gray';
                                                }
                                                ?>
                                                <td class="{{ $class }}">{{ $myscore }}</td>
                                                 <?php 
                                                  if($myscore!='')
                                                     $lastSubmittedScore = $myscore;
                                                 ?>
                                                @endforeach
                                                
                                             </tr>
                                          @endforeach
                                             
                                             
                                          </tbody>
                                       </table>
                                       <br>
                                    <hr>
                                    <br>
                                 <h4>Health Score Results compared with Baseline Value</h4>
                                 <p>The Table below shows the Health Scores for each Week and the Change in their Health when compared with the Baseline Value for each particular Question</p>
                              <br><br>
                              <div class="row">
                                 <div class="col-sm-6">
                                 <div class="row"> 
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>Enrollment Date</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>Date of Birth</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                                 </div>
                                 <div class="col-sm-4 m-t-25">
                                    <a href="" class="btn btn-default">Apply</a>
                                 </div>
                              </div>
                              </div>
                              <div class="col-sm-6 m-t-25 text-right">
                              <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a>
                              <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download PDF</a>
                              </div>
                              </div>
                           <br>
                           <br>
                           <table class="table table-flip-scroll cf">
                                          <thead class="cf">
                                             <tr>
                                                <th>Week</th>
                                                @foreach($responseArr as $response)
                                                <th>{{ $response }}</th>
                                                @endforeach
                                                <th>Baseline Value</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             @foreach($questionArr as $questionId => $question)
                                             <tr>
                                                <td>{{ $question }}</td>
                                                @foreach($responseArr as $responseId => $response)
                                                <?php
                                                $myscore ='';
                                                if(isset($submissionArr[$responseId][$questionId]))
                                                {
                                                  $myscore = $submissionArr[$responseId][$questionId];
                                                  $baseLineScore = $baseLineArr[$questionId];
                                                  
                                                  if($baseLineScore < $myscore)
                                                    $class='bg-danger';
                                                  elseif($baseLineScore > $myscore)
                                                    $class='bg-success';
                                                  else
                                                    $class='bg-warning';
                                                }
                                                else
                                                  $class='bg-gray';
                                                ?>
                                                <td class="{{ $class }}">{{ $myscore }}</td>
                                                @endforeach
                                                <td>{{ $baseLineScore }}</td>
                                             </tr>
                                          @endforeach
                                             
                                             
                                          </tbody>
                                       </table>
                                       <br>
                              <hr>
                              <br>
                              <h4>Report on Weight of the Patient</h4>
                                 <p>The Table below shows the Weight of the Patient over the period. This information has been extracted 
                                    from answer to the Question "What is your current Weight" submitted by the Patient as part of
                                    Questionnaire "Cardiac Care Project 1".</p>
                              <br><br>
                              <div class="row">
                                 <div class="col-sm-6">
                                 <div class="row"> 
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>Enrollment Date</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>Date of Birth</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                                 </div>
                                 <div class="col-sm-4 m-t-25">
                                    <a href="" class="btn btn-default">Apply</a>
                                 </div>
                              </div>
                              </div>
                              <div class="col-sm-6 m-t-25 text-right">
                              <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a>
                              <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download PDF</a>
                              </div>
                              </div>
                           <br>
                           <br>
                           <div id="line-example" style="width:100%;height:250px;"> </div>
                     </div>
                  </div>
                  
      
 

@endsection
