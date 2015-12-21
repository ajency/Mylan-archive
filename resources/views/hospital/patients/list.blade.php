@extends('layouts.single-hospital')
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
          
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div class="row">
                        <div class="col-lg-8 col-md-7">
                  <div class="page-title">
                     <h3 class="m-b-0"><span class="semi-bold">Patients</span></h3>
                     <p>(Showing all Patients under Mylan)</p>
                  </div>
                  </div>
                        <div class="col-lg-4 col-md-5 m-t-25">
                           <div class="row">
                              <div class="col-md-6">
                                 <a href="{{ url($hospital['url_slug'].'/patients/create' ) }}" class="btn btn-success"><i class="fa fa-plus"></i> Add New Patient</a>
                              </div>
                        
                           </div>
                        </div>

                  </div>

                  <div class="grid simple">
                     <div class="grid-body">

                  <table class="table table-hover table-flip-scroll cf">
                    <thead>
                        <tr>
                            <th style="width: 22%;">Reference Code</th>
                            <th class="date-sort" style="width: 12%;">Submission Reports</th>
                            <th class="date-sort" style="width: 12%;">Created On</th>
                            <th class="date-sort" style="width: 12%;">Modified On</th>
                        </tr>
                    </thead>
                    <tbody> 
                        @foreach ($patients as $patient)
                            <tr class="" >
                                <td><a href="/{{ $hospital['url_slug'] }}/patients/{{ $patient['id'] }}/edit">{{ $patient['reference_code'] }}</a></td>
                                <td><a href="/{{ $hospital['url_slug'] }}/patients/{{ $patient['id'] }}/submission-reports">Reports</a></td>
                                <td>{{ date('d/m/Y',strtotime($patient['created_at'])) }}</td>
                                <td>{{  date('d/m/Y',strtotime($patient['updated_at'])) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                                 </table>
                                  </div>
                  </div>
 
                  
      
 

@endsection
