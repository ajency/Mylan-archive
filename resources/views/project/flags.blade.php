@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Flags</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="col-md-6">
  <div class="row">
   <div class="page-title">
      <h3><span class="semi-bold">Flags</span></h3>
      <!-- <p>(Showing administrators within Royal Hospital)</p> -->
   </div>
  </div>
</div>
<div class="col-md-6 m-t-10 text-right">
  <div class="row">

   <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Patient</a>
   <a href="#" class="btn btn-danger hidden"><i class="fa fa-download"></i> Download CSV</a>

   <div class="patient-search pull-right m-r-15">
       <form name="searchData" method="GET"> 
         <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
             <option value="">-select patient-</option>
             @foreach($allPatients as $patient)
               <option   value="{{ $patient['id'] }}">{{ $patient['reference_code'] }}</option>
             @endforeach
            </select> 
         </form>
    </div>
  </div>
</div>
<div class="tabbable tabs-left">
   <div class="grid simple">
      <div>
         <!-- Nav tabs -->
         <div class="pull-right m-t-5">
                  <form name="searchData" method="GET"> 
  <input type="hidden" class="form-control" name="startDate"  >
  <input type="hidden" class="form-control" name="endDate"  >
 
   <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;margin-right:10px;">
          <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
          <span></span> <b class="caret"></b>
      </div>
                            

  </form>
  <input type="hidden" name="flag" value="0">
         </div>
         <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="{{ ($activeTab=='all')?'active':''}}"><a href="#all" aria-controls="all" role="tab" data-toggle="tab">All Flags  <i class="fa fa-flag text-muted"></i></a>
            </li>
            <li role="presentation" class="{{ ($activeTab=='red')?'active':''}}"><a href="#red" aria-controls="red" role="tab" data-toggle="tab">Red <i class="fa fa-flag text-error"></i></a>
            </li>
            <li role="presentation" class="{{ ($activeTab=='amber')?'active':''}}"><a href="#amber" aria-controls="amber" role="tab" data-toggle="tab">Amber <i class="fa fa-flag text-warning"></i></a></li>
            <li role="presentation" class="{{ ($activeTab=='green')?'active':''}}"><a href="#green" aria-controls="green" role="tab" data-toggle="tab">Green <i class="fa fa-flag text-success"></i></a></li>
         </ul>
         <!-- Tab panes -->
         <div class="tab-content">
                        <div class="row">
                  <div class="col-md-9"></div>
                  <div class="col-md-3 text-right filter-dropdown submission-filter">
                    <span class="cf-loader hidden flagsFilter pull-right"></span>
                    <form name="filterData" method="get" class="pull-right">
                    <label class="filter-label m-t-15 m-r-10">Filter</label>  
                     <select name="type" id="type" class=" select2 m-t-5 m-b-5 form-control inline filterby ">
                        <option value="">All</option>
                        <option {{ ($filterType=='previous')?'selected':''}} value="previous">Previous</option>
                        <option {{ ($filterType=='baseline')?'selected':''}} value="baseline">Baseline</option>
                     </select>
                     </form>
                     
                  </div>
                  <!--<div class="col-md-3 text-right">
                      <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;"> 
                  </div>-->
               </div>
               <hr class="m-0">
            <div role="tabpanel" class="tab-pane {{ ($activeTab=='all')?'active':''}}" id="all">
 
               <table class="table table-hover dashboard-tbl">
                  <thead>
                     <tr>
                        <th>Patient Id</th>
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
                        <td>{{ $allSubmissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $allSubmissionFlag['date'] }}</h4>
                              <sm>submission # {{ $allSubmissionFlag['sequenceNumber'] }}</sm>
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
            <div role="tabpanel" class="tab-pane {{ ($activeTab=='red')?'active':''}}" id="red">

               <table class="table table-hover dashboard-tbl">
                  <thead>
                     <tr>
                        <th>Patient Id </th>
                        <th width="20%"># Submission</th>
                        <th>Reason for Flag</th>
                        <th>Type</th>
                     </tr>
                  </thead>
                  <tbody>
                  @if(!empty($submissionFlags['flags']['red']))
                     @foreach($submissionFlags['flags']['red'] as $submissionFlag)
                      
                     <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">
                        <td>{{ $submissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                              <sm>submission # {{ $submissionFlag['sequenceNumber'] }}</sm>
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
            <div role="tabpanel" class="tab-pane {{ ($activeTab=='amber')?'active':''}}" id="amber">

        
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Patient Id</th>
                        <th width="20%"># Submission</th>
                        <th>Reason for Flag</th>
                        <th>Type</th>
                     </tr>
                  </thead>
                  <tbody>
                  @if(!empty($submissionFlags['flags']['amber']))
                     @foreach($submissionFlags['flags']['amber'] as $submissionFlag)
                      
                     <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">
                        <td>{{ $submissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                              <sm>submission # {{ $submissionFlag['sequenceNumber'] }}</sm>
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
            <div role="tabpanel" class="tab-pane {{ ($activeTab=='green')?'active':''}}" id="green">

               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Patient Id</th>
                        <th width="20%"># Submission</th>
                        <th>Reason for Flag</th>
                        <th>Type</th>
                     </tr>
                  </thead>
                  <tbody>
                  @if(!empty($submissionFlags['flags']['green']))
                      @foreach($submissionFlags['flags']['green'] as $submissionFlag)
                      
                     <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">
                        <td>{{ $submissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                              <sm>submission # {{ $submissionFlag['sequenceNumber'] }}</sm>
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
      <br>      <br>              
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

      $('select[name="referenceCode"]').change(function (event) { 
        var referenceCode = $(this).val();
        if(referenceCode!='')
            window.location.href = BASEURL+"/patients/"+referenceCode; 
      });

   });
 
  </script>
@endsection

