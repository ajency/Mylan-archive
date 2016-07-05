@extends('layouts.single-hospital')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li>
          <a href="projects.html">Patients</a>
        </li>
        <li><a href="#" class="active ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div class="page-title">
   <h3>Patient <span class="semi-bold ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</span></h3>
</div>
<div class="tabbable tabs-left">
   @include('hospital.patients.side-menu')
   <div class="tab-content">
      <div class="tab-pane table-data active" id="Patients">
         <div class="text-right">
            <a href="{{ url($hospital['url_slug'].'/patients/'.$patient['id'].'/edit' ) }}" class="btn btn-white text-success"><i class="fa fa-pencil-square-o"></i> Edit</a>
            <!-- <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a>-->
         </div> 
         <br>
         <div>
          
              
               <dl class="dl-horizontal">
                     <dt>Reference Code</dt>
                     <dd class="ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</dd>
                     <dt>Project</dt>
                     <dd>{{ $projectName }}</dd>
                     <dt>Age</dt>
                     <dd>{{ $patient['age'] }}</dd>
                     <dt>Weight</dt>
                     <dd>{{ $patient['patient_weight'] }}</dd>
                     <dt>Height</dt>
                     <dd>{{ $patient['patient_height'] }}</dd>
                     <dt>Smoker</dt>
                     <dd>{{ $patient['patient_is_smoker'] }}</dd>
                     @if($patient['patient_is_smoker']=='yes')
                     <dt>If yes, how many per week</dt>
                     <dd>{{ $patient['patient_smoker_per_week'] }}</dd>
                     @endif
                     <dt>Alcoholic</dt>
                     <dd>{{ $patient['patient_is_alcoholic'] }}</dd>
                     @if($patient['patient_is_alcoholic']=='yes')
                     <dt>Alcohol(units per week)</dt>
                     <dd>{{ $patient['patient_alcohol_units_per_week'] }}</dd>
                     @endif
                  </dl>

            <br><br>
            
               
                 
         </div>
         <br><br>
      </div>
   
   </div>
</div>

<!-- END PLACE PAGE CONTENT HERE -->
@endsection