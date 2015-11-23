@extends('layouts.master')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<ul class="breadcrumb">
    <li><a href="{{ url( 'admin/') }}">Dashboard</a> </li>
    <li><a href="#" class="active">Patients</a> </li>
 </ul>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<!-- BEGIN PAGE TITLE -->
<div class="page-title">	
    <h2><span class="semi-bold">View</span> Patients</h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="grid simple">
   
            <div class="grid-title">
                <h4>List of <span class="semi-bold">Patients</span></h4>
                <a class="btn btn-primary pull-right" href="{{ url('/admin/patients/create') }}" ><i class="fa fa-plus"></i> Add Patient</a>
            </div>
            <div class="grid-body">
                <table class="table table-bordered userList" id="example2" >
                    <thead>
                        <tr>
                            <th style="width: 22%;">Refernace Code</th>
                            <th class="date-sort" style="width: 12%;">Created On</th>
                            <th class="date-sort" style="width: 12%;">Modified On</th>
                        </tr>
                    </thead>
                    <tbody> 
                        @foreach ($patients as $patient)
                            <tr class="" >
                                <td>{{ $patient['reference_code'] }}</td>
                                <td>{{ date('d/m/Y',strtotime($patient['created_at'])) }}</td>
                                <td>{{  date('d/m/Y',strtotime($patient['updated_at'])) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
 
@endsection