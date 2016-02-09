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
            <a href="#"> Submissions</a>
         </li> 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
   <div class="pull-right">
    <form name="searchData" method="GET"> 
       <input type="hidden" class="form-control" name="startDate"  >
       <input type="hidden" class="form-control" name="endDate"  >
       <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
          <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
          <span></span> <b class="caret"></b>
       </div>

    </form>
  </div>
<div class="page-title">
     <h3>Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('project.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data" id="Submissions">
   
                        </div>
                        <div class="tab-pane table-data active" id="Flags">
                                     <ul class="nav nav-tabs" role="tablist">
                              <li role="presentation" class="active"><a href="#all" aria-controls="all" role="tab" data-toggle="tab">All Flags  <i class="fa fa-flag text-muted"></i></a>
                              </li>
                              <li role="presentation" ><a href="#red" aria-controls="red" role="tab" data-toggle="tab">Red <i class="fa fa-flag text-error"></i></a>
                              </li>
                              <li role="presentation"><a href="#amber" aria-controls="amber" role="tab" data-toggle="tab">Amber <i class="fa fa-flag text-warning"></i></a></li>
                              <li role="presentation"><a href="#green" aria-controls="green" role="tab" data-toggle="tab">Green <i class="fa fa-flag text-success"></i></a></li>
                           </ul>
                           <!-- Tab panes -->
                           <div class="tab-content">
                              <div role="tabpanel" class="tab-pane active" id="all">
                                  <div class="row">
                                    <div class="col-md-7"></div>
                                    <div class="col-md-5 text-right">
                                       <select name="role" id="role" class=" select2 m-t-5 form-control inline filterby ">
                                          <option value="2">Filter By</option>
                                          <option value="2">Previous</option>
                                          <option value="2">Baseline</option>
                                       </select>
                                    </div>
                                  
                                 </div>
                                 <hr class="">
                    <table class="table table-hover dashboard-tbl">
                      <thead>
                         <tr>
                           
                            <th width="20%"># Submission</th>
                            <th>Reason for Flag</th>
                            <th>Type</th>
                         </tr>
                      </thead>
                      <tbody>
                        @foreach($submissionFlags['all'] as $allSubmissionFlag)
                         <?php 
                          if($allSubmissionFlag['flag']=='no_colour' || $allSubmissionFlag['flag']=='')
                               continue;
                          ?>
                         <tr class="odd gradeX" >
                            <td width="110px">
                               <div class="p-l-10 p-r-20">
                                  <h4 class="semi-bold m-0 flagcount">{{ $allSubmissionFlag['date'] }}</h4>
                                  <sm>#{{ $allSubmissionFlag['sequenceNumber'] }}</sm>
                               </div>
                            </td>
                            <td>{{ $allSubmissionFlag['reason'] }}</td>
                            <td><i class="fa fa-flag text-{{ $allSubmissionFlag['flag'] }}"></i></td>
                         </tr>
                        @endforeach 
                          
                      </tbody>
                   </table>
                     
                              </div>
                                       <div role="tabpanel" class="tab-pane " id="red">
                                  <div class="row">
                                    <div class="col-md-7"></div>
                                    <div class="col-md-5 text-right">
                                       <select name="role" id="role" class=" select2 m-t-5 form-control inline filterby ">
                                          <option value="2">Filter By</option>
                                          <option value="2">Previous</option>
                                          <option value="2">Baseline</option>
                                       </select>
                                    </div>
                                  
                                 </div>
                                 <hr class="">
                    <table class="table table-hover dashboard-tbl">
                      <thead>
                         <tr>
                           
                            <th width="20%"># Submission</th>
                            <th>Reason for Flag</th>
                            <th>Type</th>
                         </tr>
                      </thead>
                    <tbody>
                       @foreach($submissionFlags['flags']['red'] as $submissionFlag)
                        
                       <tr class="odd gradeX" >
                          <td>{{ $submissionFlag['patient'] }}</td>
                          <td width="110px">
                             <div class="p-l-10 p-r-20">
                                <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                                <sm>#{{ $submissionFlag['sequenceNumber'] }}</sm>
                             </div>
                          </td>
                          <td>{{ $submissionFlag['reason'] }}</td>
                          <td><i class="fa fa-flag text-{{ $submissionFlag['flag'] }}"></i></td>
                       </tr>
                      @endforeach 
                        
                    </tbody>
                 </table>
                       
                              </div>
                                      <div role="tabpanel" class="tab-pane" id="amber">
                                 <div class="row">
                                    <div class="col-md-7"></div>
                                    <div class="col-md-5 text-right">
                                       <select name="role" id="role" class=" select2 m-t-5 form-control inline filterby ">
                                          <option value="2">Filter By</option>
                                          <option value="2">Previous</option>
                                          <option value="2">Baseline</option>
                                       </select>
                                    </div>
                                  
                                 </div>
                                 <hr class="">
                                 <table class="table table-hover">
                                    <thead>
                                       <tr>
                                          <th width="20%"># Submission</th>
                                          <th>Reason for Flag</th>
                                          <th>Type</th>
                                       </tr>
                                    </thead>
                                  <tbody>
                                     @foreach($submissionFlags['flags']['amber'] as $submissionFlag)
                                      
                                     <tr class="odd gradeX" >
                                        <td>{{ $submissionFlag['patient'] }}</td>
                                        <td width="110px">
                                           <div class="p-l-10 p-r-20">
                                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                                              <sm>#{{ $submissionFlag['sequenceNumber'] }}</sm>
                                           </div>
                                        </td>
                                        <td>{{ $submissionFlag['reason'] }}</td>
                                        <td><i class="fa fa-flag text-{{ $submissionFlag['flag'] }}"></i></td>
                                     </tr>
                                    @endforeach 
                                     
                                  </tbody>
                               </table>
                             
                              </div>
                              <div role="tabpanel" class="tab-pane" id="green">
                                 <div class="row">
                                    <div class="col-md-7"></div>
                                    <div class="col-md-5 text-right">
                                       <select name="role" id="role" class=" select2 m-t-5 form-control inline filterby ">
                                          <option value="2">Filter By</option>
                                          <option value="2">Previous</option>
                                          <option value="2">Baseline</option>
                                       </select>
                                    </div>
                                  
                                 </div>
                                 <hr class="">
                                 <table class="table table-hover">
                                    <thead>
                                       <tr>
                                          <th width="20%"># Submission</th>
                                          <th>Reason for Flag</th>
                                          <th>Type</th>
                                       </tr>
                                    </thead>
                                  <tbody>
                                     @foreach($submissionFlags['flags']['green'] as $submissionFlag)
                                      
                                     <tr class="odd gradeX" >
                                        <td>{{ $submissionFlag['patient'] }}</td>
                                        <td width="110px">
                                           <div class="p-l-10 p-r-20">
                                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                                              <sm>#{{ $submissionFlag['sequenceNumber'] }}</sm>
                                           </div>
                                        </td>
                                        <td>{{ $submissionFlag['reason'] }}</td>
                                        <td><i class="fa fa-flag text-{{ $submissionFlag['flag'] }}"></i></td>
                                     </tr>
                                    @endforeach 
                                     
                                  </tbody>
                               </table>
                             
                              </div>
                        </div>
 
                           
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>

<script type="text/javascript">
var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 
</script>
 
@endsection
