@extends('layouts.single-patient')

@section('content')
<script>
    

    
    var questionnaireIdd = '{{ $questionnaire["id"] }}';
    var questionnaireName = '{{ $hospital["project"] }}';
    var patientRefCode = '{{ $referenceCode }}';
    var RefCode = patientRefCode;
    var userToken = '{{ $parseToken }}';
    var hospitalLogo = "{{ $hospital['logoUrl'] }}";
    var hospitalName = "{{ $hospital['name'] }}";
    var hospitalPhone = "{{ $hospital['phone'] }}";
    var hospitalEmail = "{{ $hospital['email'] }}";
    var Url = "{{url()}}";
    var hospitalIdd = "{{ $hospital['id'] }}";
    var projectIdd = "{{ $hospital['project_id'] }}";
    var hospitalAddress = "{{ $hospital['address'] }}";

    var APP_ID       = "{{ config('constants.parse_sdk.app_id') }}"

    var APP_AuthrizationKey       = "{{ env( 'APP_AuthrizationKey') }}"
    var APP_KEY       = "{{ env( 'APP_KEY') }}"
    
    var JS_KEY       = "{{ env( 'JS_KEY') }}"


    var PARSE_SERVER_URL       = "{{ env( 'PARSE_SERVER_URL') }}parse"

    var AUTH_HEADERS;

AUTH_HEADERS = {
  headers: {
    "X-API-KEY": APP_KEY,
    "X-Authorization": APP_AuthrizationKey,
    "Content-Type": 'application/json'
  }
};




    var REST_API_KEY = "{{ config('constants.parse_sdk.rest_api_key') }}"
   

 // Parse.initialize(APP_ID, JS_KEY);
    Parse.initialize(APP_ID);
    Parse.serverURL = "{{ env( 'PARSE_SERVER_URL') }}parse";

     Parse.User.become(userToken).then(function(user) {
                  console.log('became user');
                  console.log(userToken);
                 });


</script>



<div ng-app="angularApp">

  <nav class="navbar navbar-default hospital-nav" >
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation  </span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#dashboard">{{ hospitalImageExist($hospital,false) }}</a>
       
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
       <ul class="nav navbar-nav navbar-right">
          <li><a href=""><span class="badge-round green"><i class="fa fa-phone"></i></span>&nbsp;&nbsp;<span class="text-muted"> Call Us</span>&nbsp;&nbsp;{{ $hospital['phone'] }}</a></li>
          <li ng-controller="headerCtrl" ng-include src="'patients/views/notificationcount.html'">
           <!--  <div ng-include src="'patients/views/notificationcount.html'"> -->
          <!--   <a href="#notification" ng-init="view.init()">
              <div ng-include src="'patients/views/notificationcount.html'" class="notification" ></div>
              <i class="fa fa-bell"></i>&nbsp;&nbsp;
              <span class="text-muted">Notifications</span>
            </a> -->
        <!--   </div> -->
          </li>
           <li class="dropdown">
                <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="font-size: 23px;">              
                <i class="fa fa-cog text-muted"></i> <span class="text-muted"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#reset-password"><i class="fa fa-refresh text-muted"></i> Password Reset</a></li>
                  <li><a href="{{ url( 'auth/logout' ) }}"><i class="fa fa-power-off text-muted"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

  <div style-container class="container_main" ng-view> </div>

  <div class="footer">
    <div class="container">
      <div class="footer-content text-center">
        <span>Copyright © 2016 NHS Hospital. All rights reserved.</span>
      </div>
    </div>
  </div>
</div>
<script>
    /*BACK Button overide*/
    window.onload = function () {
      if (typeof history.pushState === "function") {
        history.pushState("jibberish", null, null);
        window.onpopstate = function () {
          history.pushState('newjibberish', null, null);
          // Handle the back (or forward) buttons here
          // Will NOT handle refresh, use onbeforeunload for this.
        };
      }
      else {
        var ignoreHashChange = true;
        window.onhashchange = function () {
          if (!ignoreHashChange) {
            ignoreHashChange = true;
            window.location.hash = Math.random();
            // Detect and redirect change here
            // Works in older FF and IE9
            // * it does mess with your hash symbol (anchor?) pound sign
            // delimiter on the end of the URL
          }
          else {
            ignoreHashChange = false;   
          }
        };
      }
    }
  </script>
  
@endsection
