@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="{{ url( 'admin/hospitals' ) }}" >{{ $hospital['name'] }}</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Patients</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


<div class="page-title">
                     <h3><span class="semi-bold">Patients</span></h3>
                      <p>(Patients under {{ $hospital['name'] }})</p>
                  </div>
                  <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                           <table class="table table-hover" id="example" >
                              <thead>
                                 <tr>
                                    <th>Referece Code</th>
                                    <th>Created Date</th>
                                  </tr>
                              </thead>
                              <tbody>
                              <?php 
                              $project = '';
                              ?>
                              @foreach($patientsData as $patient)
                                 @if($project!=$patient['projectName'])
                                 <tr class="odd gradeX info" >
                                    <td colspan="2" class="text-center">{{ $patient['projectName'] }}</td>
                                 </tr>
                                 @endif
                                 <tr class="odd gradeX" >
                                    <td class="ttuc patient-refer{{ $patient['referenceCode'] }}">{{ $patient['referenceCode'] }}</td>
                                    <td>{{ $patient['date'] }}</td>
                                 </tr>
                                 <?php 
                                 $project = $patient['projectName'];
                                 ?>
                              @endforeach
                                 
                             </tbody>
                           </table>
                        </div>
                     </div>

@endsection