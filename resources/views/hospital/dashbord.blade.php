@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


      <div class="page-title">
         <h3 class="m-b-0"><span class="semi-bold">Dashboard</span> </h3>
         <p>(Showing Real-Time Hospital data at a Glance)</p>
      </div>
      <div class="grid simple">
         <div class="grid-body no-border">
            <br>
            <div class="row">
               <div class="col-md-6">
                  <h4><span class="semi-bold"> Flags</span> Summary</h4>
                  <p>(Showing overall Flags Summary)</p>
                  <br>
                  <div id="line-example" style="width:100%;height:250px;"> </div>
               </div>
               <div class="col-md-6">
                  <h4><span class="semi-bold">Comparison</span>  of Projects</h4>
                  <p>(Showing Open Flags for each Project)</p>
                  <br>
                  <div id="placeholder-bar-chart" style="height:250px"></div>
               </div>
            </div>
            <br>
            <br>
            <div class="row">
               <div class="col-md-6">
                  <h4><span class="semi-bold"> Submission</span> Details</h4>
                  <p>(Showing overall Submissions and Missed Submissions)</p>
                  <br>
                  <div id="sparkline-pie" class="col-md-12"></div>
               </div>
               <div class="col-md-6">
                  <h4><span class="semi-bold">Comparison</span>  of Projects</h4>
                  <p>(Showing Response Rate of each Project)</p>
                  <br>
                  <div id="placeholder-bar-chart1" style="height:250px"></div>
               </div>
            </div>   
         </div>
      </div>
      <div class="grid simple">
         <div class="grid-body no-border">
          <br>
            <h4>Recent <span class="semi-bold"> Activities</span></h4>
            <p>(Keeping you updated)</p>
            <br>
            <ul class="list-group">
               <li class="list-group-item"><i class="fa fa-check text-success"></i> <a href="#">Patient 123</a> was flagged.</li>
               <li class="list-group-item"><i class="fa fa-check text-success"></i> New user <a href="#">Mr.Willington</a> created.</li>
               <li class="list-group-item"><i class="fa fa-check text-success"></i> New Hospital was created and will be managed by Mr.Moon</li>
               <li class="list-group-item"><i class="fa fa-check text-success"></i> <a href="#">Patient 456</a> was discharged from ward number 5.</li>
               <li class="list-group-item"><i class="fa fa-check text-success"></i>New user <a href="#">Ms.Annez</a> created.</li>
               <li class="list-group-item"><i class="fa fa-check text-success"></i> Existing user <a href="#">Mr.Willington</a> was removed.</li>
            </ul>
         </div>
      </div>

@endsection