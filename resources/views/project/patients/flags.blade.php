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
   <input type="hidden" name="flag" value="0">
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
                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3 text-right filter-dropdown m-t-15 submission-filter">
                                     <span class="cf-loader hidden flagsFilter pull-right"></span>
                                     <form name="filterData" method="get" class="pull-right">
                                     <label class="filter-label m-t-15 m-r-10">Filter</label>  
                                       <select name="type" id="type" class=" select2  form-control inline filterby ">
                                          <option value="">All</option>
                                          <option {{ ($filterType=='previous')?'selected':''}} value="previous">Previous</option>
                                          <option {{ ($filterType=='baseline')?'selected':''}} value="baseline">Baseline</option>
                                       </select>                                       
                                       </form>
                                       
                                    </div>
                                  
                                 </div>
                                 <hr class="">
                              <div role="tabpanel" class="tab-pane active" id="all">
                                  
                    <table class="table table-hover dashboard-tbl">
                      <thead>
                         <tr>
                           
                            <th width="20%"># Submission</th>
                            <th>Reason for Flag</th>
                            <th>Type</th>
                         </tr>
                      </thead>
                      <tbody>
                      @if(!empty($submissionFlags['all']))   
                        @foreach($submissionFlags['all'] as $allSubmissionFlag)
                         <?php 
                          if($allSubmissionFlag['flag']=='no_colour' || $allSubmissionFlag['flag']=='')
                               continue;
                          ?>
                         <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $allSubmissionFlag['responseId'] }}';">
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
                      @else 
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif       
                      </tbody>
                   </table>
                     
                              </div>
                                       <div role="tabpanel" class="tab-pane " id="red">
               
                    <table class="table table-hover dashboard-tbl">
                      <thead>
                         <tr>
                           
                            <th width="20%"># Submission</th>
                            <th>Reason for Flag</th>
                            <th>Type</th>
                         </tr>
                      </thead>
                    <tbody>
                    @if(!empty($submissionFlags['flags']['red']))  
                       @foreach($submissionFlags['flags']['red'] as $submissionFlag)
                        
                       <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">
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
                    @else 
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif      
                    </tbody>
                 </table>
                       
                              </div>
                                      <div role="tabpanel" class="tab-pane" id="amber">
                           
                                 <table class="table table-hover">
                                    <thead>
                                       <tr>
                                          <th width="20%"># Submission</th>
                                          <th>Reason for Flag</th>
                                          <th>Type</th>
                                       </tr>
                                    </thead>
                                  <tbody>
                                  @if(!empty($submissionFlags['flags']['amber']))  
                                     @foreach($submissionFlags['flags']['amber'] as $submissionFlag)
                                      
                                     <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">
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
                                 @else 
                                  <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                                @endif       
                                  </tbody>
                               </table>
                             
                              </div>
                              <div role="tabpanel" class="tab-pane" id="green">
                       
                                 <table class="table table-hover">
                                    <thead>
                                       <tr>
                                          <th width="20%"># Submission</th>
                                          <th>Reason for Flag</th>
                                          <th>Type</th>
                                       </tr>
                                    </thead>
                                  <tbody>
                                  @if(!empty($submissionFlags['flags']['green']))  
                                     @foreach($submissionFlags['flags']['green'] as $submissionFlag)
                                      
                                     <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">  
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
                                  @else 
                                   <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                                   @endif      
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

   $(document).ready(function() {

      $('select[name="type"]').change(function (event) { 
        $(".flagsFilter").removeClass('hidden');
         $('form').submit();
      });

   });
</script>
 
@endsection
