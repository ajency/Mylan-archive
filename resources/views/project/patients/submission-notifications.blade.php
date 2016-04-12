@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}">{{ $patient['reference_code']}}</a> </li>
        <li><a href="#" class="active">Submissions Notification</a> </li>
 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="m-r-15 pull-right patient-search">
<select class="selectpicker" data-live-search="true" title="Patient" name="referenceCode">
      <option value="">-select patient-</option>
       @foreach($allPatients as $patientData)
         <option {{($patient['reference_code']==$patientData['reference_code'])?'selected':''}}  value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
       @endforeach
      </select> 
</div>
<div class="page-title">
     <h3>Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('project.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data active" id="Submissions">
                        <div class="grid simple grid-table">
                    <div class="grid-title no-border">
                       <h4>
                            Submission <span class="semi-bold">Notifications</span> 
                          <sm class="light">(These are the notifications generated for submissions)</sm>
                       </h4>
                       <div class="tools">
                       <label class="filter-label">Filter</label>
                     <form method="get">  
                     <select name="reviewStatus" id="reviewStatus" class="pull-right select2 m-t-5 m-b-20 form-control inline filterby pull-right">
                                <option value="all">All</option>
                                <option {{ ($reviewStatus=='reviewed_no_action')?'selected':''}} value="reviewed_no_action">Reviewed - No action</option>
                                <option {{ ($reviewStatus=='reviewed_call_done')?'selected':''}} value="reviewed_call_done">Reviewed - Call done</option>
                                <option {{ ($reviewStatus=='reviewed_appointment_fixed')?'selected':''}} value="reviewed_appointment_fixed">Reviewed - Appointment fixed</option>
                                <option {{ ($reviewStatus=='unreviewed')?'selected':'' }} value="unreviewed">Unreviewed</option>
                                <!-- <option {{ ($reviewStatus=='missed')?'selected':'' }} value="missed">Missed</option> -->
                             </select>
                     <span class="cf-loader hidden m-t-3 submissionFilter"></span>
                     </form>
                     
                  </div>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                       <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
                          <thead class="cf">
                             <tr>
                               <th class="sorting "># Submission<br><br></th>
                                
                                <th class="sorting">Reason<br><br>
                                </th>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody id="submissionData" limit="5" object-type="submission" object-id="0">
                           
                          @if(!empty($submissionNotifications))   
                              @foreach($submissionNotifications['alertMsg'] as $submissionNotification)
            
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$submissionNotification['responseId']}}';">
                                
                                    <td class="text-center">
                                      <h4 class="semi-bold m-0 flagcount">{{ $submissionNotification['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submissionNotification['sequenceNumber'] }}</b></sm>
                                   </td>
                                   <td class="text-center text-success">{{ sprintf($submissionNotification['msg'], $submissionNotification['previousTotalRedFlags'],$submissionNotification['sequenceNumber'] ) }}</td> 
                                   
                                   <td class="text-center text-success"><div class="submissionStatus" @if(strlen($submissionNotification['reviewStatus']) >10 ) data-toggle="tooltip" @endif data-placement="top" title="{{ getStatusName($submissionNotification['reviewStatus']) }}">{{ getStatusName($submissionNotification['reviewStatus']) }}</div></td>
                                </tr>
                                 
                        
                            @endforeach
                        @else 
                        <tr><td class="text-center no-data-found" colspan="20"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
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
 
   $(document).ready(function() {

      $('select[name="reviewStatus"]').change(function (event) { 
        $(".submissionFilter").removeClass('hidden');
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
