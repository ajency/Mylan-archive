@extends('layouts.single-hospital')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li>
          <a href="projects.html">Patients</a>
        </li>
        <li><a href="#" class="active">{{ $patient['reference_code']}}</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div class="page-title">
   <h3>Patient <span class="semi-bold">{{ $patient['reference_code']}}</span></h3>
</div>
<div class="tabbable tabs-left">
   @include('hospital.patients.side-menu')
   <div class="tab-content">
      <div class="tab-pane table-data active" id="Patients">
         <!-- <div class="text-right">
            <a href="#" class="btn btn-white text-success"><i class="fa fa-pencil-square-o"></i> Edit</a>
            <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a>
         </div> -->
         <br>
         <div>
          
              
               <dl class="dl-horizontal">
                     <dt>Reference Code</dt>
                     <dd>{{ $patient['reference_code']}}</dd>
                     <dt>Project</dt>
                     <dd>{{ $projectName }}</dd>
                  </dl>
               
               
          
              
                  
              
            <br><br>
            
               
                 
         </div>
         <br><br>
      </div>
   
   </div>
</div>

<!-- END PLACE PAGE CONTENT HERE -->
@endsection