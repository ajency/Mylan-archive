@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" class="ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
        <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Setup</a> Devices </li>
 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="m-r-15 pull-right patient-search">
<select class="selectpicker" data-live-search="true" title="Patient" name="referenceCode">
      <option class="ttuc" value="">-select patient-</option>
       @foreach($allPatients as $patientData)
         <option class="ttuc patient-refer{{ $patientData['reference_code'] }}" {{($patient['reference_code']==$patientData['reference_code'])?'selected':''}}  value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
       @endforeach
      </select> 
</div>
<div class="page-title">
     <h3>Patient <span class="semi-bold ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('project.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data active" id="Submissions">
                        <div class="grid simple grid-table">
                         @if(!empty($userDevices))  
						               <input type="button" value="Reset set up devices" class="clear-data" id="{{ $patient['id'] }}"/>
                          @endif 
						
                    <div class="grid-title no-border">
                       <h4>
                            Setup Devices
                          <!-- <sm class="light">(These are the notifications generated for submissions)</sm> -->
                       </h4>
                       <select class="pull-right" style="margin-top: -7px;" name="deviceStatus">
                           <option value="all-device-data"> -Select status- </option>
                           <option value="archived-device-data"> Archived </option>
                           <option value="new-device-data"> New device </option>
                        </select>
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
                               <th class="sorting ">Status</th> 
                             </tr>
                          </thead>
                          <tbody id="all-device-data">
                           
                          @if(!empty($userDevices))   
                              @foreach($userDevices as $userDevice)
                                 <tr>                                
                                   <td class="text-center">{{ date('d-m-Y',strtotime($userDevice['created_at'])) }}</td>
                                   <td class="text-center">{{ $userDevice['device_identifier'] }}</td> 
                                   <td class="text-center">{{ $userDevice['device_type'] }}</td>   
                                   <td class="text-center">{{ $userDevice['device_os'] }}</td> 
                                   <td class="text-center">{{ $userDevice['access_type'] }}</td> 
                                   <td class="text-center">{{ $userDevice['status'] }}</td> 
                                </tr>
                            @endforeach
                        @else 
                           <tr><td class="text-center no-data-found" colspan="6"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif    
                                
                          </tbody>

                          <tbody id="archived-device-data">
                           
                          @if(!empty($userDevices)) 
                            <?php
                              $archivedCounter = 0;
                            ?>  
                              @foreach($userDevices as $userDevice)
                                @if( $userDevice['status'] == "Archived" )
                                  <?php
                                    $archivedCounter = 1;
                                  ?>
                                 <tr>                                
                                   <td class="text-center">{{ date('d-m-Y',strtotime($userDevice['created_at'])) }}</td>
                                   <td class="text-center">{{ $userDevice['device_identifier'] }}</td> 
                                   <td class="text-center">{{ $userDevice['device_type'] }}</td>   
                                   <td class="text-center">{{ $userDevice['device_os'] }}</td> 
                                   <td class="text-center">{{ $userDevice['access_type'] }}</td> 
                                   <td class="text-center">{{ $userDevice['status'] }}</td> 
                                </tr>
                                @endif
                            @endforeach
                            @if( $archivedCounter == 0)
                                <tr><td class="text-center no-data-found" colspan="6"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                            @endif 
                        @else 
                           <tr><td class="text-center no-data-found" colspan="6"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif    
                                
                          </tbody>

                          <tbody id="new-device-data">
                           
                          @if(!empty($userDevices))  
                            <?php
                              $newdeviceCounter = 0;
                            ?>  
                              @foreach($userDevices as $userDevice)
                                 @if( $userDevice['status'] == "New device" )
                                    <?php
                                      $newdeviceCounter = 1;
                                    ?> 
                                 <tr>                                
                                   <td class="text-center">{{ date('d-m-Y',strtotime($userDevice['created_at'])) }}</td>
                                   <td class="text-center">{{ $userDevice['device_identifier'] }}</td> 
                                   <td class="text-center">{{ $userDevice['device_type'] }}</td>   
                                   <td class="text-center">{{ $userDevice['device_os'] }}</td> 
                                   <td class="text-center">{{ $userDevice['access_type'] }}</td> 
                                   <td class="text-center">{{ $userDevice['status'] }}</td> 
                                </tr>
                                @endif
                            @endforeach
                            @if( $newdeviceCounter == 0)
                                <tr><td class="text-center no-data-found" colspan="6"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                            @endif 
                        @else 
                           <tr><td class="text-center no-data-found" colspan="6"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
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
      $("tbody#archived-device-data").addClass("hidden");
      $("tbody#new-device-data").addClass("hidden");
      $('select[name="reviewStatus"]').change(function (event) { 
        $(".submissionFilter").removeClass('hidden');
         $('form').submit();
      });

       $('select[name="referenceCode"]').change(function (event) { 
        var referenceCode = $(this).val();
        if(referenceCode!='')
            window.location.href = BASEURL+"/patients/"+referenceCode; 
      });

       $('select[name="deviceStatus"]').change(function(e){
          if($('select[name="deviceStatus"]').val() == "all-device-data"){
              $("tbody#all-device-data").removeClass("hidden");
              $("tbody#archived-device-data").addClass("hidden");
              $("tbody#new-device-data").addClass("hidden");
          }else if($('select[name="deviceStatus"]').val() == "archived-device-data"){
              $("tbody#archived-device-data").removeClass("hidden");
              $("tbody#all-device-data").addClass("hidden");
              $("tbody#new-device-data").addClass("hidden");
          }else{
              $("tbody#new-device-data").removeClass("hidden");
              $("tbody#archived-device-data").addClass("hidden");
              $("tbody#all-device-data").addClass("hidden");
          }
       });

   });
  </script>
 
@endsection
