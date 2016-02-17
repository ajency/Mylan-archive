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
              <tr><td class="text-center" colspan="3">No data found</td></tr>
            @endif 
              </tbody>
           </table>
      </div>
      </div>
      <div class="tab-pane " id="Reports">

      </div> 
   </div>
   </div>

 
@endsection
