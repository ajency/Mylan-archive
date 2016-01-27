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
   <div class="page-title">
      <h3><span class="semi-bold">Flags</span></h3>
      <!-- <p>(Showing administrators within Royal Hospital)</p> -->
   </div>
</div>
<div class="col-md-6 m-t-10 text-right">
   <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Patient</a>
   <a href="#" class="btn btn-danger hidden"><i class="fa fa-download"></i> Download CSV</a>
</div>
<div class="tabbable tabs-left">
   <div class="grid simple">
      <div>
         <!-- Nav tabs -->
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
                  <div class="col-md-2 text-right">
                     <select name="role" id="role" class="pull-right select2 m-t-5 form-control inline filterby pull-right">
                        <option value="2">Filter By</option>
                        <option value="2">Previous</option>
                        <option value="2">Baseline</option>
                     </select>
                  </div>
                  <div class="col-md-3 text-right">
                     <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;">
                  </div>
               </div>
               <hr class="m-0">
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
                    @foreach($submissionFlags['all'] as $allSubmissionFlag)
                     <?php 
                      if($allSubmissionFlag['flag']=='no_colour' || $allSubmissionFlag['flag']=='')
                           continue;
                      ?>
                     <tr class="odd gradeX" >
                        <td>{{ $allSubmissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $allSubmissionFlag['date'] }}</h4>
                              <sm>Seq - {{ $allSubmissionFlag['sequenceNumber'] }}</sm>
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
                  <div class="col-md-2 text-right">
                     <select name="role" id="role" class="pull-right select2 m-t-5 form-control inline filterby pull-right">
                        <option value="2">Filter By</option>
                        <option value="2">Previous</option>
                        <option value="2">Baseline</option>
                     </select>
                  </div>
                  <div class="col-md-3 text-right">
                     <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;">
                  </div>
               </div>
               <hr class="m-0">
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
                     @foreach($submissionFlags['flags']['red'] as $submissionFlag)
                      
                     <tr class="odd gradeX" >
                        <td>{{ $submissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                              <sm>Seq - {{ $submissionFlag['sequenceNumber'] }}</sm>
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
                  <div class="col-md-2 text-right">
                     <select name="role" id="role" class="pull-right select2 m-t-5 form-control inline filterby pull-right">
                        <option value="2">Filter By</option>
                        <option value="2">Previous</option>
                        <option value="2">Baseline</option>
                     </select>
                  </div>
                  <div class="col-md-3 text-right">
                     <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;">
                  </div>
               </div>
               <hr class="m-0">
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
                     @foreach($submissionFlags['flags']['amber'] as $submissionFlag)
                      
                     <tr class="odd gradeX" >
                        <td>{{ $submissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                              <sm>Seq - {{ $submissionFlag['sequenceNumber'] }}</sm>
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
                  <div class="col-md-2 text-right">
                     <select name="role" id="role" class="pull-right select2 m-t-5 form-control inline filterby pull-right">
                        <option value="2">Filter By</option>
                        <option value="2">Previous</option>
                        <option value="2">Baseline</option>
                     </select>
                  </div>
                  <div class="col-md-3 text-right">
                     <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;">
                  </div>
               </div>
               <hr class="m-0">
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
                      @foreach($submissionFlags['flags']['green'] as $submissionFlag)
                      
                     <tr class="odd gradeX" >
                        <td>{{ $submissionFlag['patient'] }}</td>
                        <td width="110px">
                           <div class="p-l-10 p-r-20">
                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                              <sm>Seq - {{ $submissionFlag['sequenceNumber'] }}</sm>
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
      <br>      <br>              
   </div>
</div>
@endsection

