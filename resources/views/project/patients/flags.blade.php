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
                        <div class="tab-pane table-data active" id="Submissions">
   
                        </div>
                        <div class="tab-pane table-data active" id="Flags">
                         
                          <table class="table table-hover" id="example">
              <thead>
                 <tr>
                    <th class="hidden">Patient</th>
                    <th class="hidden">Doctor</th>
                    <th>Submission #</th>
                    <th>Reason for Flag</th>
                    <th>Type</th>
                    <th>Date</th>
                    
                 </tr>
              </thead>
              <tbody>
         
               @foreach($patientFlags as $patientFlag)
                <?php 
                  if($patientFlag['flag']=='no_colour' || $patientFlag['flag']=='')
                       continue;
                  ?>
                 <tr class="odd gradeX">
                    <td>{{ $patientFlag['sequenceNumber'] }}</td>
                    <td>{{ $patientFlag['reason'] }}</td>
                    <td><i class="fa fa-flag text-{{ $patientFlag['flag'] }}"></i></td>
                    <td>{{ $patientFlag['date'] }}</td>
                 </tr>
              @endforeach
                                               
              </tbody>
           </table>       <br>
                           
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>


 
@endsection
