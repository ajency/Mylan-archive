
@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" class="active ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
         
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<a class="btn btn-primary pull-right" id="btnSave" title="Download this page as a printable PDF"><i class="fa fa-print"></i> Get PDF
<span class="addLoader"></span></a>
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
         <option class="ttuc patient-refer{{ $patientData['reference_code'] }}" {{($patient['reference_code']==$patientData['reference_code'])?'selected':''}}  value="{{ $patientData['id'] }}" >{{ $patientData['reference_code'] }}</option>
       @endforeach
      </select> 
</div>
<div class="page-title">
   <h3>Patient <span class="semi-bold ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>
</div>
<div class="tabbable tabs-left" id="page1">
      @include('project.patients.side-menu')
     <div class="tab-content">
     <div class="tab-pane table-data active" id="Patients" style="padding: 15px 8px;">
        <div class="pgHeight"> <!--test -->
        <div class="row">
                <div class="col-sm-8">
                  <!-- <dl class="dl-horizontal">
                 <dt>Reference Code d</dt>
                 <dd>{{ $patient['reference_code']}}</dd>
                 <dt>Age</dt>
                 <dd>{{ $patient['age'] }}</dd>
                
                @if(isset($patient['project_attributes']) && !empty($patient['project_attributes']))
                 @foreach($patient['project_attributes'] as $label => $value)
                 <dt>{{ $label }}</dt>
                 <dd>
                    @if(is_array($value))
                        @if(isset($value['multiple']))
                           {{ implode(", ",$value['multiple']) }} 
                        @else
                          @foreach($value as $default => $val)
                              @if($val!='')
                                {{ $val }} {{ $default }}
                              @else 
                                &nbsp;     
                              @endif
                          @endforeach
                        @endif
                    @else
                      @if($value!='')
                        {{ $value }} 
                      @else 
                        -    
                      @endif 
            
                    @endif
                 </dd>
                 @endforeach
                @endif 
                <dt>Smoker</dt>
                 <dd style="text-transform: capitalize;">{{ $patient['patient_is_smoker'] }}</dd>
                 @if($patient['patient_is_smoker']=='yes')
                 <dt>If yes, how many per week</dt>
                 <dd>{{ $patient['patient_smoker_per_week'] }}</dd>
                 @endif
    
                 <dt>Alcohol (units per week)</dt>
                 <dd>{{ $patient['patient_alcohol_units_per_week'] }}</dd> 
           

              </dl> -->

              <!-- test -->
              <div class="col-sm-12">
                <div class="row m-b-15">
                  <div class="col-sm-6">
                    <strong>Reference Code</strong>
                  </div>
                  <div class="col-sm-6 ttuc">
                    {{ $patient['reference_code']}}
                  </div>
                </div>

                <div class="row m-b-15">
                  <div class="col-sm-6">
                    <strong>Age</strong>
                  </div>
                  <div class="col-sm-6">
                    {{ $patient['age'] }}
                  </div>
                </div>
				<?php
                    foreach ($multipleAttr as $Mkeys => $Mvalues) {
                      if(!(array_key_exists($Mvalues['label'], $patient['project_attributes']))){
                       $patient['project_attributes'][$Mvalues['label']] = array($Mvalues['label'] => '');
                      }
                    }
                 ?>	
                 @if(isset($patient['project_attributes']) && !empty($patient['project_attributes']))
                 @foreach($patient['project_attributes'] as $label => $value)
                 <div class="row m-b-15">
                   <div class="col-sm-6">
                     <strong>{{ $label }}</strong>
                   </div>
                   <div class="col-sm-6">
                     @if(is_array($value))
                        @if(isset($value['multiple']))
                           {{ implode(", ",$value['multiple']) }} 
                        @else
                          @foreach($value as $default => $val)
                              @if($val!='')
                                {{ $val }} {{ $default }}
                              @else 
                                &nbsp;     
                              @endif
                          @endforeach
                        @endif
                    @else
                      @if($value!='')
                        {{ $value }} 
                      @else 
                        -    
                      @endif 
            
                    @endif
                   </div>
                 </div>
                 @endforeach
                @endif

                <div class="row m-b-15">
                  <div class="col-sm-6">
                  <strong>Smoker</strong>
                  </div>
                  <div class="col-sm-6">
                    <span class="ttc">{{ $patient['patient_is_smoker'] }}</span>
                  </div>
                </div>
                
                 @if($patient['patient_is_smoker']=='yes')
                <div class="row m-b-15">
                   <div class="col-sm-6">
                     <strong>If yes, how many per week</strong>
                   </div>
                   <div class="col-sm-6">
                     {{ $patient['patient_smoker_per_week'] }}
                   </div>
                 </div>
                 @endif 

                 <div class="row m-b-15">
                   <div class="col-sm-6">
                     <strong>Alcohol (units per week)</strong>
                   </div>
                   <div class="col-sm-6">
                     {{ $patient['patient_alcohol_units_per_week'] }}
                   </div>
                 </div>
                 

              </div>
              <!-- /test -->
              </div>
              <div class="col-sm-4">
                @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
                 <div class="text-right">
                    <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/edit' ) }}" class="btn btn-white text-success"><i class="fa fa-pencil-square-o"></i> Edit</a>
                    <!-- <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a> -->
                 </div>
                @endif
              </div>
           </div>
           <br>
           <div class="grid simple ">
              <div class="grid simple grid-table">
                 <div class="grid-title no-border">
                    <a href="patient-submissions.html">
                       <h4>Response <span class="semi-bold">Rate</span></h4>
                    </a>
                 </div>
              </div>
           </div>
           <div class="row">
              <div class="col-sm-5 b-r">
                 <div class="">
                
                    <div id="submissionschart"></div>
                 
                    <div class="row p-t-20 chart-val">
                       <div class="col-md-4 text-center">
                          <h3 class="no-margin bold">{{  $responseRate['completed'] }}%</h3>
                          <p class=" text-underline">{{  $responseRate['completedCount'] }} Submissions Completed</p>
                       </div>
                       <div class="col-md-4 text-center">
                          <h3 class="no-margin bold">{{  $responseRate['late'] }}%</h3>
                          <p class="">{{  $responseRate['lateCount'] }} Submissions Late</p>
                       </div>
                       <div class="col-md-4 text-center">
                          <h3 class="no-margin bold">{{  $responseRate['missed'] }}%</h3>
                          <p class="">{{  $responseRate['missedCount'] }} Submissions Missed</p>
                       </div>
                    </div>
                 </div>
              </div>
              <!--      <div class="col-sm-1 ">
                 </div> -->
              <div class="col-sm-7">
                 <select class="pull-right" name="generateChart">
                    <option value="total_score" selected>Total Score</option>
                    <option value="red_flags" > Red Flags</option>
                    <option value="amber_flags">  Amber Flags</option>
                    <option value="green_flags">Green Flags</option>
                 </select>
                  @if(!$totalResponses)
                   <table class="table table-flip-scroll table-hover dashboard-tbl">
                  <tbody>
                  <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                  </tbody>
                  </table>
                  @else
                    <div id="chartdiv"></div>
                  @endif
              </div>
           </div>
           </div> <!-- test height --> 
           <h4 class="p-h-c">Patient health chart</h4>
           <p>Patient health chart shows the comparison between
              1.Patients current score to baseline score- indicated by highlighted cell
              2.Patients current score to previous score indicated by flag
              Red-indicates current score is worse then previous/baseline score by 2 points
              Amber-indicates current score is worse then previous/baseline score by 1 point
              Green-indicates current score is better then previous/baseline score
              White-Indicates current score is same as baseline score.
              Flag is not displayed if the current score is same as previous score
           </p>
          
            <div class="tableOuter">
            <div class="compared-to">
              <span><i class="fa fa-stop"></i> Compared to Baseline</span>               
              <span><i class="fa fa-flag"></i> Compared to Previous</span>               
            </div>           
            <div class="x-axis-text">Submissions</div>
           <div class="y-axis-text">Questions</div>
           <div class="table-responsive {{(!empty($responseArr))?'sticky-table-outer-div':''}} {{(count($responseArr)!=0 && count($responseArr)>10)?'sticky-tableWidth':''}}"> 
           
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
                           @foreach($flagsQuestions as $questionId => $question)
                           <tr>
                              <td class="headcol">{{ $question }}</td>
                              @foreach($responseArr as $responseId => $response)
                              <?php
                              if(isset($submissionFlags[$responseId][$questionId]))
                              {
                                $class='bg-'.$submissionFlags[$responseId][$questionId]['baslineFlag'];
                                $flag= ($submissionFlags[$responseId][$questionId]['previousFlag']=='no_colour' || $submissionFlags[$responseId][$questionId]['previousFlag']=='')?'hidden':'text-'.$submissionFlags[$responseId][$questionId]['previousFlag'];
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
           <div>
               
              <hr>
              <br>
              <div class="q-g"></div>
              <div class="pull-left">
                 <h4 class="bold">Questionnaire Graph</h4>
              </div>
              <select class="pull-right" name="generateQuestionChart">
                    @foreach($questionLabels as $questionId =>$question)
                    <option value="{{ $questionId }}" >{{ $question }}</option>
                    @endforeach
                 </select>
               @if(!$totalResponses)
                   <table class="table table-flip-scroll table-hover dashboard-tbl">
                  <tbody>
                  <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                  </tbody>
                  </table>
              @else
                <div id="questionChart" class="p-t-20" style="width:100%; height:400px;"></div>
              @endif
              <br><br> 
              <div class="s-s">
                 <div class="grid simple grid-table">
                    <div class="grid-title no-border">
                       <h4>
                          Submissions <span class="semi-bold">Summary</span> 
                          <sm class="light">(These are scores & flags for current submissions)</sm>
                       </h4>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                       <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
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
                                <th class="sorting">Alerts<br><br>
                                </th>
                                <th class="sorting">Status<br><br>
                                </th>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody id="submissionData" limit="5" object-type="patient-submission" object-id="{{ $patient['reference_code']}}">
                          <div class="loader-outer hidden">
                            <span class="cf-loader"></span>
                         </div>
                           @if(!empty($submissionsSummary))      
                              @foreach($submissionsSummary as $responseId=> $submission)
                                 @if($submission['status']=='missed' || $submission['status']=='late')
                                    <tr>
                                       <td>
                                         <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                         <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                      </td>
                                     
                                     <td class="text-right">0</td>
                                     <td class="text-center">0</td>
                                     <td class="text-left">0</td>
                                      
                                      <td class="text-right semi-bold margin-none flagcount p-h-0">
                                         <h4>-</h4>
                                      </td>
                                      <td class="text-center semi-bold margin-none flagcount p-h-0">
                                         <h4>/</h4>
                                      </td>
                                      <td class="text-left semi-bold margin-none flagcount p-h-0">
                                         <h4>-</h4>
                                      </td>
                                    
                                      <td class="text-right  text-error">0</td>
                                      <td class="text-center  text-warning">0</td>
                                      <td class="text-left   text-success">0</td>
                                
                                      <td class="text-right text-error">0</td>
                                      <td class="text-center text-warning">0</td>
                                      <td class="text-left  text-success">0</td>

                                      <td class="text-center text-success">-</td>
                                      <td class="text-center text-success">{{ getStatusName($submission['status']) }}</td>
                                      <td class="text-center text-success">-</td>
                                   </tr>
                                 @else 

                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
                                    <td>
                                      <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                   </td>
                                  
                                  <td class="text-right">{{ $submission['baseLineScore'] }}</td>
                                  <td class="text-center">{{ $submission['previousScore'] }}</td>
                                  <td class="text-left">{{ $submission['totalScore'] }}</td>
                                  
                                  <td class="text-right semi-bold margin-none flagcount p-h-0">
                                      <h4><b class="text-{{ $submission['totalBaseLineFlag'] }}">{{ $submission['comparedToBaslineScore'] }}</b></h4>
                                  </td>
                                  <td class="text-center semi-bold margin-none flagcount p-h-0">
                                    <h4><b>/</b></h4>
                                  </td> 
                                  <td class="text-left semi-bold margin-none flagcount p-h-0">
                                      <h4><b class="f-w text-{{ $submission['totalPreviousFlag'] }}">{{ $submission['comparedToPrevious'] }}</b></h4>
                                  </td>

                                   <td class="text-right text-error">{{ $submission['previousFlag']['red'] }}</td>
                                   <td class="text-center text-warning">{{ $submission['previousFlag']['amber'] }}</td>
                                   <td class="text-left  text-success">{{ $submission['previousFlag']['green'] }}</td>
                            
                                   <td class="text-right text-error">{{ $submission['baseLineFlag']['red'] }}</td>
                                   <td class="text-center text-warning">{{ $submission['baseLineFlag']['amber'] }}</td>
                                   <td class="text-left  text-success">{{ $submission['baseLineFlag']['green'] }}</td>
                                   
                                   <td class="text-center text-success">{{ $submission['alert'] }}</td>  
                                   <td class="text-center text-success">{{ getStatusName($submission['status']) }}</td>
                                   <td class="text-center text-success"><div class="submissionStatus" @if(strlen($submission['reviewed']) >10 ) data-toggle="tooltip" @endif data-placement="top" title="{{ getStatusName($submission['reviewed']) }}">{{ getStatusName($submission['reviewed']) }}</div></td>
                                </tr>
                                @endif
                        
                            @endforeach
                           @else 
                        <tr><td class="text-center no-data-found" colspan="16"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif    
                                
                          </tbody>
                       </table>
                       <hr style="margin: 0px 0px 10px 0px;">
					   @if($countSummarySubmissionView > 5)
						   <div class="text-right {{ (empty($submissionsSummary))?'hidden':'' }}">
							  <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/submissions') }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
						   </div>
					   @endif	   
                    </div>
                 </div>
              </div>

               <div class="grid simple grid-table s-n-r">
                           <div class="grid-title no-border">
                         <h4>
                          Submission Notification  <span class="semi-bold">Report</span> 
                          <sm class="light">(These are the notifications generated for submissions)</sm>
                       </h4>
                           </div>
                           <div class="grid-body no-border" style="display: block;">
                              <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
                          <thead class="cf">
                             <tr>
                                <!-- <th class="sorting" width="10%">Patient ID <br><br></th> -->
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
                              @foreach($submissionNotifications['alertMsg'] as $submissionNotification)
            
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/{{$submissionNotification['URL']}}';">
                                    <!-- <td class="text-center">{{ $submissionNotification['patient'] }}</td> -->
                                    <td class="text-center">
                                      <h4 class="semi-bold m-0 flagcount">{{ $submissionNotification['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submissionNotification['sequenceNumber'] }}</b></sm>
                                   </td>
                                   <td class="text-center text-success">{{ $submissionNotification['msg'] }}</td> 
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
                              <hr style="margin: 0px 0px 10px 0px;">
                              <div class="text-right {{ (empty($submissionsSummary))?'hidden':'' }}">
								 @if((!empty($submissionNotifications['alertMsg'])) && ($viewAllsubmissionNotifications > 5))
									<a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/submission-notifications') }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
								@endif   	
                              </div>
                           </div>
                        </div
           </div>
           <!-- <a href="patient-flag.html">    Open Red Flags</a></h4>
              <span>( 5 recently generated red flags )</span> -->
           <!--    <div class="grid simple ">
              <div class="grid simple grid-table">
                  <div class="grid-title no-border">
                    <a href="patient-submissions.html"> <h4>Submissions <span class="semi-bold">(5 recent submissions)</span></h4></a>
                  </div>
              </div>
              </div> -->
           <!-- submission -->
           <div class="grid simple grid-table flg">
              <div class="grid-title no-border">
                 <h4><span class="semi-bold">FLAGS</span></h4>
              </div>
              <div class="row">
                 <div class="col-sm-12">
                    <table class="table table-hover dashboard-tbl">
                       <thead>
                          <tr>
                             <th width="30%"># Submission</th>
                             <th>Reason for Flag</th>
                             <th>Type</th>
                          </tr>
                       </thead>
                       <tbody>
                       <?php 
                          $i=1;
						  $hideVAll = "";
                        ?>
                        @if(!empty($patientFlags['all']))      
                           @foreach($patientFlags['all'] as $allSubmissionFlag)
                         <?php
                          if($allSubmissionFlag['flag']=='no_colour' || $allSubmissionFlag['flag']=='')
                               continue;
                           
                            if($i==6){
                              break;
							  $hideVAll = 'style="display:block;"';
							 }else{
								 $hideVAll = 'style="display:none;"';
							 }
                          ?>
                         <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $allSubmissionFlag['responseId'] }}';">
                            <td width="110px">
                               <div class="p-l-10 p-r-20">
                                  <h4 class="semi-bold m-0 flagcount">{{ $allSubmissionFlag['date'] }}</h4>
                                  <sm>#{{ $allSubmissionFlag['sequenceNumber'] }}</sm>
                               </div>
                            </td>
                            <td><?php echo $allSubmissionFlag['reason'] ?></td>
                            <td><i class="fa fa-flag text-{{ $allSubmissionFlag['flag'] }}"></i></td>
                         </tr>
                         <?php 
                          $i++;
                          ?>
                        @endforeach 
                       @else 
                        <tr><td class="text-center no-data-found" colspan="3"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif 
                    </table>
                    <hr style="margin: 0px 0px 10px 0px;">
					
                    <div class="text-right {{ (empty($patientFlags['all']))?'hidden':'' }}" <?php echo $hideVAll; ?>>
                       <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/flags') }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
                    </div>
                 </div>
              </div>
              <!-- <h6>This patient has missed 5 consecutive submissions</h6> -->
              <!-- <h6>This patient has missed 5 consecutive submissions</h6> -->
           </div>
           <br><br>
           </div>
        <div class="tab-pane" id="Submissions">

        </div>
        <div class="tab-pane" id="Reports">
        </div>
     </div>
  </div>
 
 <?php 

$questionId = current(array_keys($questionLabels));
$inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
$questionLabel = (isset($questionLabels[$questionId]))?$questionLabels[$questionId]:'';
//$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;


?>
    <script type="text/javascript">
    var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
    var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 


   $(document).ready(function() {
  
     // Always scroll to right 
    $('.sticky-table-outer-div').animate({scrollLeft: 99999}, 300);
    
    // submission chart
    var legends = {score: "Total Score"};
    // lineChartWithOutBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,"chartdiv",'Submissions','Total Score');
    lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,0,"chartdiv",'Submissions','Total Score');

    //question chart
    shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$questionLabel}}',0,'questionChart','Submissions','Score');
 
    drawPieChart("submissionschart",<?php echo  $responseRate['pieChartData']; ?>),1;
        
    $('select[name="generateChart"]').change(function (event) { 
      if($(this).val()=='total_score')
      { 
       legends = {score: "Total Score"};
        lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,0,"chartdiv",'Submissions','Total Score');
       // var legends = {score: "Total Score"};
       //  lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,"chartdiv",'Submissions','Total Score');
      }
      else if($(this).val()=='red_flags')
      { 
        legends = {Baseline: "Baseline Flags",Previous: "Previous Flags"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['redFlags'];?>,legends,"chartdiv",'Submissions','Total Red Flags');
      }
      else if($(this).val()=='amber_flags')
      {
        legends = {Baseline: "Baseline Flags",Previous: "Previous Flags"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['amberFlags'];?>,legends,"chartdiv",'Submissions','Total Amber Flags');

      }
      else if($(this).val()=='green_flags')
      {
        legends = {Baseline: "Baseline Flags",Previous: "Previous Flags"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['greenFlags'];?>,legends,"chartdiv",'Submissions','Total Green Flags');

      } 

    });

     $('select[name="generateQuestionChart"]').change(function (event) { 
      <?php 
      foreach($questionLabels as $questionId => $label)
      {
        $inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
        //$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;
        ?>
        if($(this).val()=='{{$questionId}}')
        { 
          shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$label}}',0,'questionChart','Submissions','Score');
        }

        <?php
      }
      ?>

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
      if($(".pgHeight").height() < 700){$(".p-h-c").css("padding-top","250px");}
       
      $(".q-g").css("padding-top","470px"); 
      $(".s-s").css("padding-top","400px"); 
      $(".s-n-r").css("margin-top","450px"); 
      $(".flg").css("margin-top","500px"); 
       
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
                doc.save( 'Patient Summary.pdf');﻿
             }
          });
            setInterval(function(){ 
              $(".addLoader").removeClass("cf-loader"); 
              $("#page1").css("background","");
              $(".p-h-c").css("padding-top","0px"); 
              $(".q-g").css("padding-top","0px"); 
              $(".s-s").css("padding-top","0px"); 
              $(".s-n-r").css("margin-top","0px"); 
              $(".flg").css("margin-top","0px"); 
            }, 3000);   
      });
    });  
    

    
</script>
<style>
#submissionschart canvas{
  margin-left: 0px !important;
  margin-top: 0px !important;
}

</style>
<!-- END PLACE PAGE CONTENT HERE -->
@endsection
