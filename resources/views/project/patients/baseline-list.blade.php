@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
 
      <li><a href="#">Patients</a></li>
        <li><a href="#">{{ $patient['reference_code']}}</a> </li>
        <li><a href="#" class="active">Baseline Score</a> </li>
          
 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="page-title">
     <h3>Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
     <div class="pull-right m-r-15 patient-search">
          <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
          <option value="">-select patient-</option>
           @foreach($allPatients as $patientData)
             <option   value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
           @endforeach
          </select>
       </div>
     
  </div>
 <div class="tabbable tabs-left">
    @include('project.patients.side-menu')
   <div class="tab-content">
      <div class="tab-pane table-data" id="Patients">
      </div>
      <div class="tab-pane table-data" id="Submissions">
           
      </div>
      <div class="tab-pane active" id="baseline">

         <h4><span class="semi-bold">Base Lines</span></h4>
         <p>(Baseline score for Patient Id {{ $patient['reference_code']}})</p>
         <br>
          <div class="pull-right">
           @if($isQuestionnaireSet)
              <a class="btn btn-white" href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/base-line-score-edit') }}"><span class="text-success"><i class="fa fa-pencil-square-o"></i> Add</span></a>
           @endif
          </div>
           <table class="table table-hover table-flip-scroll cf">
              <thead class="cf">
                 <tr>
                    <th>Sequence Number</th>
                    <th>Created Date</th>
                 </tr>
              </thead>

              <tbody>
              @if(!empty($baseLines))  
               @foreach($baseLines as $responseId => $baseLine)
                 <tr onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/base-line-score/'.$responseId) }}'">
                    <td>#{{ $baseLine['sequenceNumber'] }}</td>
                    <td>{{ $baseLine['date'] }}</td>
                 </tr>  
              @endforeach                                     
            @else 
              <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
            @endif 
              </tbody>
           </table>
      </div>
      </div>
      <div class="tab-pane " id="Reports">

      </div> 
   </div>
   </div>

 <script type="text/javascript">
   

   $(document).ready(function() {

 
       $('select[name="referenceCode"]').change(function (event) { 
        var referenceCode = $(this).val();
        if(referenceCode!='')
            window.location.href = BASEURL+"/patients/"+referenceCode; 
      });

   });
  </script>
@endsection
