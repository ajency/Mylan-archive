@extends('layouts.single-patient')

@section('content')
<script>
    

    
    var questionnaireIdd = '{{ $questionnaire["id"] }}';
    var questionnaireName = '{{ $questionnaire["name"] }}'
    var patientRefCode = '{{ $referenceCode }}';
    var RefCode = patientRefCode;
    var userToken = '{{ $parseToken }}';
    var hospitalLogo = "{{ $hospital['logo'] }}";
    // console.log({{ $hospital['name'] }});

    var APP_ID       = "{{ config('constants.parse_sdk.app_id') }}"
    var JS_KEY       = "{{ env( 'JS_KEY') }}"
    var REST_API_KEY = "{{ config('constants.parse_sdk.rest_api_key') }}"

 Parse.initialize(APP_ID, JS_KEY);

     Parse.User.become(userToken).then(function(user) {
                  console.log('became user');
                  console.log(userToken);
                 });


</script>



<div ng-app="angularApp">

  <nav class="navbar navbar-default hospital-nav" ng-controller="headerCtrl">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><img src="<?php echo $hospital['logo'] ?>" width="auto" height="45px"></a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
       <ul class="nav navbar-nav navbar-right">
          <li><a href="#"><span class="badge-round green"><i class="fa fa-phone"></i></span>&nbsp;&nbsp;<span class="text-muted"> CALL US</span>&nbsp;&nbsp;0161 123 1234</a></li>
         <li><a href="#notification"><div class="notification">2</div><i class="fa fa-bell"></i>&nbsp;&nbsp;<span class="text-muted">Notifications</span></a></li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

  <div style-container class="container_main" ng-view> </div>

  <div class="footer">
    <div class="container">
      <div class="footer-content text-center">
        <span>Copyright © 2015 NHS Hospital. All rights reserved.</span>
      </div>
    </div>
  </div>


</div>
  
@endsection
