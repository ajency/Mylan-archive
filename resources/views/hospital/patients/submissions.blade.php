@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active" > HOME</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Patients</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Submissions</a>
         </li> 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="page-title">
     <h3>Patient Id<span class="semi-bold ttuc"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('hospital.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data active" id="Submissions">
                            <table class="table table-flip-scroll tbl-summary table-hover">
               <thead class="cf">
                   <tr>
                       <th>Patient ID</th>
                       <th><span class="p-l-13 p-r-13">When</span></th>
                       <th>Compared To Previous</th>
                       <th>Compared To Baseline</th>
                       <th>Previous Flag Status</th>
                       <th>Baseline Flag Status</th>
                   </tr>
               </thead>
               <tbody>
                @foreach($submissionFlags as $responseId => $submissionFlag)
                   <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/submissions/{{$responseId}}';">
                       <td class="ttuc patient-refer{{ $submissionFlag['patient'] }}">{{ $submissionFlag['patient'] }}</td>
                       <td width="110px">
                           <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                               <h4 class="text-muted no-margin bold"> {{ $submissionFlag['sequenceNumber'] }} <span class="sm text-muted"> On {{ $submissionFlag['occurrenceDate'] }} </span></h4>
                           </div>
                       </td>
                       <td>
                           <div class="lst-sub">
                               <h3 class="bold ">
                     {{ $submissionFlag['baselineScore'] }}
                     <span class="sm">  
                      {{ count($submissionFlag['baseLineFlag']['green']) }} &nbsp;<i class="fa fa-flag text-success"></i> &nbsp;/&nbsp;
                      {{ count($submissionFlag['baseLineFlag']['red']) }}  &nbsp;<i class="fa fa-flag text-warning"></i> &nbsp;/&nbsp;
                       {{ count($submissionFlag['baseLineFlag']['amber']) }} &nbsp;<i class="fa fa-flag text-error"></i> 
                     </span>
                     </h3>
                           </div>
                       </td>
                       <td>
                           <div class="lst-sub">
                               <h3 class="bold ">
                    {{ $submissionFlag['previousScore'] }} 
                     <span class="sm">  
                     {{ count($submissionFlag['previosFlag']['green']) }} &nbsp;<i class="fa fa-flag text-success"></i> &nbsp;/&nbsp;
                      {{ count($submissionFlag['previosFlag']['red']) }} &nbsp;<i class="fa fa-flag text-warning"></i> &nbsp;/&nbsp;
                       {{ count($submissionFlag['previosFlag']['amber']) }} &nbsp;<i class="fa fa-flag text-error"></i> 
                     </span>
                     </h3>
                           </div>
                       </td>
                       <td><span class=" text-warning">{{ $submissionFlag['previousFlagStatus'] }}</span></td>
                       <td><span class=" text-warning">{{ $submissionFlag['baseLineFlagStatus'] }}</span></td>
                   </tr>
                   @endforeach
                    
               </tbody>
           </table>
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>


 
@endsection
