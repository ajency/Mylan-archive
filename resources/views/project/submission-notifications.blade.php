@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="#" class="active">Submission Notifications</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


<div class="page-title">
                     <h3><span class="semi-bold">Submission Notification</span> Report</h3>    
                     <!-- <p>(Click on any Patient ID to see Profile Details)</p> -->

                     <div class="patient-search pull-right">
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
                  <div class="grid simple">
                     <div class="grid-body no-border table-data">
                        <br>
          
                        <!-- Chart - Added -->
                        <div class="row">
                           <div class="col-md-9"></div>
                           <div class="col-md-3 text-right filter-dropdown submission-filter">
                              <span class="cf-loader hidden submissionFilter"></span>
                              <form method="get"> 
                              <label class="filter-label m-t-15 m-r-10">Filter</label>                              
                             <select name="reviewStatus" id="reviewStatus" class="pull-right select2 m-t-5 m-b-20 form-control inline filterby pull-right">
                                <option value="all">All</option>
                                <option {{ ($reviewStatus=='reviewed_no_action')?'selected':''}} value="reviewed_no_action">Reviewed - No action</option>
                                <option {{ ($reviewStatus=='reviewed_call_done')?'selected':''}} value="reviewed_call_done">Reviewed - Call done</option>
                                <option {{ ($reviewStatus=='reviewed_appointment_fixed')?'selected':''}} value="reviewed_appointment_fixed">Reviewed - Appointment fixed</option>
                                <option {{ ($reviewStatus=='unreviewed')?'selected':'' }} value="unreviewed">Unreviewed</option>
                                <!-- <option {{ ($reviewStatus=='missed')?'selected':'' }} value="missed">Missed</option> -->
                             </select>
                             
                             </form>
                             
                           </div>
                          <!-- <div class="col-md-3 text-right">
                               <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;"> 
                           </div>-->
                        </div>
                        <div class="alert alert-info alert-black">
                          
                           Submission Notifications Report
                          <sm class="light">(These are the notifications generated for submissions)</sm>
                        
                        </div>
                        <div class="grid-body no-border" style="display: block;padding: 10px 5px;">
                          <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
                          <thead class="cf">
                             <tr>
                                <th class="sorting" width="10%">Patient ID <br><br></th>
                                <th class="sorting "># Submission<br><br></th>
                                
                                <th class="sorting" width="40%">Reason<br><br>
                                </th>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody id="submissionData" limit="5" object-type="submission" object-id="0">
                           
                          @if(!empty($submissionNotifications['alertMsg']))   
                              @foreach($submissionNotifications['alertMsg'] as $submissionNotification)
            
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$submissionNotification['responseId']}}';">
                                    <td class="text-center">{{ $submissionNotification['patient'] }}</td>
                                    <td class="text-center">
                                      <h4 class="semi-bold m-0 flagcount">{{ $submissionNotification['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submissionNotification['sequenceNumber'] }}</b></sm>
                                   </td>
                                   <td class="text-center text-success">{{ sprintf($submissionNotification['msg'], $submissionNotification['previousTotalRedFlags'],$submissionNotification['sequenceNumber'] ) }}</td> 
                                   
                                   <td class="text-center text-success">
                                   <!-- <div class="submissionStatus" @if(strlen($submissionNotification['reviewStatus']) >10 ) data-toggle="tooltip" @endif data-placement="top" title="{{ getStatusName($submissionNotification['reviewStatus']) }}">{{ getStatusName($submissionNotification['reviewStatus']) }}</div> -->
                                    <div class="submissionStatus" style="width: 100%;">{{ getStatusName($submissionNotification['reviewStatus']) }}</div>
                                   </td>
                                </tr>
                                 
                        
                            @endforeach
                        @else 
                        <tr><td class="text-center no-data-found" colspan="20"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif    
                                
                          </tbody>
                       </table>
                       </div>
                        <hr style="    margin: 0px 0px 10px 0px;">
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

