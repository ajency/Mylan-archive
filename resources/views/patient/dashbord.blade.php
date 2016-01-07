@extends('layouts.single-patient')

@section('content')
<script>
    var patientRefCode = {{ $referenceCode }};
    var RefCode = patientRefCode.toString();
    var userToken = '{{ $parseToken }}';
    console.log(patientRefCode);

</script>

<!-- <div> -->
  <!-- <input type="button" ng-click="view.getCategories()"> -->

  <!-- <div id="maincontent">
            <div id="view" ng-view></div>
  </div>

</div> -->
<!-- <div class="container dashboard" ng-app="myApp" ng-controller="myCtrl" >
    <div class="alert alert-success" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     You do not have any Questionnaries to be submitted now.
    Submitt: <input type="button" ng-click="deleteComment()"><br>
     <div class="form-group">
            <input type="text" class="form-control input-sm" name="author" ng-model="lastName" placeholder="Name">
        </div>

<br>
  </div>
      <div class="panel panel-default" ng-init="">
         <div class="panel-heading"><h3>{{ $questionnaire['name'] }}</h3></div>
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-8 col-xs-24">
                <img src="{{ url('patients/images/cardiac-care.png') }}" class="img-responsive">
              </div>
              <div class="col-sm-8 col-xs-12">
                <br class="hidden-xs"><br class="hidden-sm hidden-xs"><br class="hidden-sm hidden-xs">
                <div class="b-b">
                <h1>40%</h1>
                <h4 class="text-muted"><span><i class="fa fa-circle text-muted"></i> 40 Questions Missed</span></h4>
                </div>
              </div>
              <div class="col-sm-8 col-xs-12">
                <br class="hidden-xs"><br class="hidden-sm hidden-xs"><br class="hidden-sm hidden-xs">
                <div class="b-b">
                <h1>60%</h1>
                <h4 class="text-muted"><span><i class="fa fa-circle text-warning"></i> 60 Questions Missed</span></h4>
              </div>
              </div>
            </div>
          </div>
      </div>
      <br>
      <br class="hidden-xs">
      <h3>Questionnaire Summary</h3>
      <br>
       <div class="panel panel-default">
      <div class="bg-white">
         
         <div class="panel-body b-b-g">
         <div class="row">
              <div class="col-sm-12">
                <h4><i class="fa fa-arrow-circle-up text-success"></i>&nbsp;&nbsp;Upcoming</h4>
              </div>
              <div class="col-md-9 col-sm-8">
                <h4 class="text-muted"><span><i class="fa fa-calendar"></i> 25 October 2015</span></div>
              </h4>
              <div class="col-md-3 col-sm-4 text-right">
                
              </div>
            </div>
            </div>
          </div>
            
           
         <div class="panel-body b-b-g">
             <div class="row">
              <div class="col-sm-12">
                <h4><i class="fa fa-times-circle text-danger"></i>&nbsp;&nbsp;Due</h4>
              </div>
              <div class="col-md-9 col-sm-8">
                <h4 class="text-muted"><span><i class="fa fa-calendar"></i> 25 October 2015</span></h4>
              </div>
              <div class="col-md-3 col-sm-4 text-right">
                <button type="button" class="btn btn-success btn-block">Start Now</button>
              </div>
            </div>
            </div>
            
            
           
         <div class="panel-body b-b-g">
             <div class="row">
              <div class="col-sm-12 col-xs-24">
                <h4><i class="fa fa-minus-circle text-muted"></i>&nbsp;&nbsp;Missed</h4>
              </div>
              <div class="col-md-9 col-sm-8 col-xs-24">
                <h4 class="text-muted"><span><i class="fa fa-calendar"></i> 25 October 2015</span></h4>
              </div>
              <div class="col-md-3 col-sm-4 col-xs-24 text-right">
                </div>
            </div>
              </div>
            
            
            
           <div class="panel-body">
               <div class="row">
                <div class="col-sm-12 col-xs-24">
                  <h4><i class="fa fa-check-circle text-warning"></i>&nbsp;&nbsp;Submitted</h4>
                </div>
                <div class="col-md-9 col-sm-8 col-xs-24">
                  <h4 class="text-muted"><span><i class="fa fa-calendar"></i> 25 October 2015</span></h4>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-24 text-right">
                  <button type="button" class="btn btn-warning btn-block">View</button>
                </div>
              </div>
              </div>
            </div>
            </div>

          
  </div> -->

  <div ng-app="angularApp">
      <div ng-view></div>   
  </div>
  
@endsection
