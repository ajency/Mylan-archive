@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" class="patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
        <li><a href="#" class="active">Setup</a> Devices </li>
 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="m-r-15 pull-right patient-search">
<select class="selectpicker" data-live-search="true" title="Patient" name="referenceCode">
      <option value="">-select patient-</option>
       @foreach($allPatients as $patientData)
         <option class="patient-refer{{ $patientData['reference_code'] }}" {{($patient['reference_code']==$patientData['reference_code'])?'selected':''}}  value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
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
                            Setup Devices
                          <!-- <sm class="light">(These are the notifications generated for submissions)</sm> -->
                       </h4>
                       <div class="tools">
                    
                     
                  </div>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                       <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
                          <thead class="cf">
                             <tr>
                               <th class="sorting ">Date</th>
                               <th class="sorting ">Device Identifier</th>
                               <th class="sorting ">Device Type</th>
                               <th class="sorting ">Device OS</th>
                               <th class="sorting ">Access Type</th> 
                             </tr>
                          </thead>
                          <tbody>
                           
                          @if(!empty($userDevices))   
                              @foreach($userDevices as $userDevice)
                                 <tr>                                
                                   <td class="text-center">{{ date('d-m-Y',strtotime($project['created_at'])) }}</td>
                                   <td class="text-center">{{ $userDevice['device_identifier'] }}</td> 
                                   <td class="text-center">{{ $userDevice['device_type'] }}</td>   
                                   <td class="text-center">{{ $userDevice['device_os'] }}</td> 
                                   <td class="text-center">{{ $userDevice['access_type'] }}</td> 
                                </tr>
                            @endforeach
                        @else 
                           <tr><td class="text-center no-data-found" colspan="5"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
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
