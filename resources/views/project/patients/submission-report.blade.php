@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> HOME</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Patients</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"  class="active" > Reports</a>
         </li> 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 <div class="tabbable tabs-left">
                     <ul class="nav nav-tabs inner-tabs" id="tab-2">
                        <li class=""><a href="patient-summary.html"><i class="fa fa-wheelchair"></i> Summary</a></li>
                        <li><a href="patient-submissions.html"><i class="fa fa-list-alt"></i> Submissions</a></li>
                        <li class=""><a href="baseline-score.html"><i class="fa fa-bar-chart"></i> Baseline Score</a></li>
                        <li class="active"><a href="#Reports"><i class="fa fa-bar-chart"></i> Reports</a></li>
                        <li class=""><a href="patient-details.html"><i class="fa fa-bar-chart"></i> Details</a></li>
                     </ul>
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane" id="Submissions">
                           
                        </div>
                        <div class="tab-pane active" id="Reports">
                            <h4>Health Score Results compared with Previous Scores</h4>
                                 <p>The Table below shows the Health Scores for each Week and the Change in their Health when compared with previous Score</p>
                             <br><br>
                             <div class="row">
                                 <div class="col-sm-6">
                                 <div class="row"> 
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>Start Date</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>End Date</label>
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
                                                <th>1 Jun</th>
                                                <th>8 Jun</th>
                                                <th>15 Jun</th>
                                                <th>22 Jun</th>
                                                <th>29 Jun</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             <tr>
                                                <td>Pain</td>
                                                <td class="bg-warning" data-toggle="modal" data-target="#myModal">4</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-warning">4</td>
                                                <td class="bg-danger">5</td>
                                                <td class="bg-danger"><i class="fa fa-flag text-error"></i> 10</td>
                                             </tr>
                                             <tr>
                                                <td>Weight</td>
                                                <td class="bg-warning">3</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-danger">3</td>
                                                <td class="bg-success">4</td>
                                                <td class="bg-success">2</td>
                                             </tr>
                                             <tr>
                                                <td>Apetite</td>
                                                <td class="bg-warning">2</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-warning"><i class="fa fa-flag text-warning"></i> 3</td>
                                                <td class="bg-warning">3</td>
                                                <td class="bg-warning">3</td>
                                                
                                             </tr>
                                            <tr>
                                                <td>Diabetes</td>
                                                <td class="bg-warning">4</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-success">3</td>
                                                <td class="bg-success">2</td>
                                                <td class="bg-warning">1</td>
                                             </tr>
                                             
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
                                    <label>Start Date</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>End Date</label>
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
                                                <th>1 Jun</th>
                                                <th>8 Jun</th>
                                                <th>15 Jun</th>
                                                <th>22 Jun</th>
                                                <th>29 Jun</th>
                                                <th>Baseline Value</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             <tr>
                                                <td>Pain</td>
                                                <td class="bg-warning"  data-toggle="modal" data-target="#myModal">4</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-warning">4</td>
                                                <td class="bg-danger">5</td>
                                                <td class="bg-danger"><i class="fa fa-flag text-error"></i> 10</td>
                                                <td>8</td>
                                             </tr>
                                             <tr>
                                                <td>Weight</td>
                                                <td class="bg-warning">3</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-danger">3</td>
                                                <td class="bg-success">4</td>
                                                <td class="bg-success">2</td>
                                                <td>4</td>
                                             </tr>
                                             <tr>
                                                <td>Apetite</td>
                                                <td class="bg-warning">2</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-warning"><i class="fa fa-flag text-warning"></i> 3</td>
                                                <td class="bg-warning">3</td>
                                                <td class="bg-warning">3</td>
                                                <td>7</td>
                                             </tr>
                                            <tr>
                                                <td>Diabetes</td>
                                                <td class="bg-warning">4</td>
                                                <td class="bg-gray"></td>
                                                <td class="bg-success">3</td>
                                                <td class="bg-success">2</td>
                                                <td class="bg-warning">1</td>
                                                <td>6</td>
                                             </tr>
                                             
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
                                    <label>Start Date</label>
                                    <div class="input-append default date" style="width:100%;">
                                       <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                                       <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-row">
                                    <label>End Date</label>
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
                     </div>

                  <div class="page-title">
                     <h3>Report of Patient <span class="semi-bold  ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>
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
