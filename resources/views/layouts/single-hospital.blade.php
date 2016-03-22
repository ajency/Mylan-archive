<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
      <meta charset="utf-8" />
      <title>{{ $hospital['name'] }} Administrator</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
      <meta content="" name="description" />
      <meta content="" name="author" />
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <!-- BEGIN PLUGIN CSS -->
      <link href="{{ asset('hospital/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css" media="screen"/>
      <link href="{{ asset('hospital/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
      <link rel="stylesheet" href="{{ asset('hospital/plugins/jquery-ricksaw-chart/css/rickshaw.css') }}" type="text/css" media="screen">
      <link rel="stylesheet" href="{{ asset('hospital/plugins/jquery-morris-chart/css/morris.css') }}" type="text/css" media="screen">
      <!-- END PLUGIN CSS -->
      <!-- BEGIN CORE CSS FRAMEWORK -->
      <link href="{{ asset('hospital/plugins/boostrapv3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('hospital/plugins/boostrapv3/css/bootstrap-theme.min.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('hospital/plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('hospital/css/animate.min.css') }}" rel="stylesheet" type="text/css"/>
      <!-- END CORE CSS FRAMEWORK -->
      <link href="{{ asset('plugins/bootstrap-datepicker/css/datepicker.css') }}" rel="stylesheet" type="text/css" media="screen"/>
      
      <link href="{{ asset('plugins/jquery-multiselect/jquery.multiselect.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" type="text/css"/>

      <!-- BEGIN CSS TEMPLATE -->
      <link href="{{ asset('hospital/css/style.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('hospital/css/responsive.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('hospital/css/custom-icon-set.css') }}" rel="stylesheet" type="text/css"/>
      <!-- END CSS TEMPLATE -->
      <script src="{{ asset('bower_components/jquery/dist/jquery.js') }}"></script>
      <script src="{{ asset('bower_components/amcharts/dist/amcharts/amcharts.js') }}"></script>
      <script src="{{ asset('bower_components/amcharts/dist/amcharts/serial.js') }}"></script>
      <script src="{{ asset('bower_components/amcharts/dist/amcharts/themes/light.js') }}"></script>
      <script>
         var HOSPITAL_ID = 0;
         var PATIENT_ID = 0;
         var BASEURL = '{{ url() }}/{{ $hospital["url_slug"] }}';
      </script>
   </head>
   <!-- END HEAD -->
   <!-- BEGIN BODY -->
   <body class="horizontal-menu">
      <!-- BEGIN HEADER -->
      <div class="header navbar navbar-inverse ">
         <!-- BEGIN TOP NAVIGATION BAR -->
         <div class="navbar-inner">
            <div class="header-seperation">
               <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">
                  <li class="dropdown">
                     <a id="horizontal-menu-toggle" href="#"  class="" >
                        <div class="iconset top-menu-toggle-white"></div>
                     </a>
                  </li>
               </ul>
               <!-- BEGIN LOGO --> 
               <a href="{{ url() }}/{{ $hospital['url_slug'] }}">{{ hospitalImageExist($hospital,false) }}</a>
               <!-- END LOGO --> 
               <ul class="nav pull-right notifcation-center">
                  <li class="dropdown" id="header_task_bar">
                     <a href="{{ url($hospital['url_slug'].'/' ) }}" class="dropdown-toggle active" data-toggle="">
                        <div class="iconset top-home"></div>
                     </a>
                  </li>
                  <li class="dropdown" id="header_inbox_bar" >
                     <a href="email.html" class="dropdown-toggle" >
                        <div class="iconset top-messages"></div>
                        <span class="badge" id="msgs-badge">2</span> 
                     </a>
                  </li>
                  <li class="dropdown" id="portrait-chat-toggler" style="display:none">
                     <a href="#sidr" class="chat-menu-toggle">
                        <div class="iconset top-chat-white "></div>
                     </a>
                  </li>
               </ul>
            </div>
            <!-- END RESPONSIVE MENU TOGGLER --> 
            <div class="header-quick-nav container text-center" >
               <!-- BEGIN TOP NAVIGATION MENU -->
               <a href="{{ url($hospital['url_slug'].'/' ) }}" class="pull-left">{{ hospitalImageExist($hospital,false) }}</a>
               <!-- END TOP NAVIGATION MENU -->
               <!-- BEGIN CHAT TOGGLER -->
               <div class="pull-right">
                  <div class="chat-toggler">
                     <a href="#">
                        <div class="user-details">
                           <div class="username">
                              {{ Auth::user()->name }}    
                              <span class="badge badge-default">{{ userType() }}</span>               
                           </div>
                        </div>
                     </a>
                  </div>
                  <ul class="nav quick-section ">
                     <li class="quicklinks">
                        <a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">
                           <div class="iconset top-settings-dark "></div>
                        </a>
                        <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
                           <li><a href="user-profile.html"> My Account</a>
                           </li>
                           <li class="divider"></li>
                           <li><a href="{{ url($hospital['url_slug'].'/logout' ) }}"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
                        </ul>
                     </li>
                  </ul>
               </div>
               <!-- END CHAT TOGGLER -->
            </div>
            <!-- END TOP NAVIGATION MENU --> 
         </div>
         <!-- END TOP NAVIGATION BAR --> 
      </div>
      <!-- END HEADER -->
      <!-- BEGIN CONTAINER -->
      <div class="page-container row-fluid">
         <!-- BEGIN PAGE CONTAINER-->
         <div class="page-content">
            <div class="bar">
               <div class="container">
                  <div class="bar-inner">
                     <ul>
                       <!--  <li class="{{ ( $active_menu == 'dashbord')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/dashbord/' ) }}">
                           <span><i class="fa fa-tachometer"></i> Dashboard </span>
                           </a>
                        </li> -->
                        <li class="{{ ( $active_menu == 'project')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/projects/' ) }}">
                           <span><i class="fa fa-hospital-o"></i> Projects </span>
                           </a>
                        </li>
                        <!-- <li class="{{ ( $active_menu == 'patients')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/patients/' ) }}">
                           <span><i class="fa fa-wheelchair"></i> Patients </span>
                           </a>
                        </li> -->
                        <li class="{{ ( $active_menu == 'users')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/users/' ) }}">
                           <span><i class="fa fa-users"></i> Users </span>
                           </a>
                        </li>
         
                       <!--  <li class="{{ ( $active_menu == 'submission')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/submissions/' ) }}">
                           <span><i class="fa fa-list-alt"></i> Submissions </span>
                           </a>
                        </li>
                        <li>
                           <a href="javascript:;">
                           <span><i class="fa fa-bar-chart"></i> Reports </span>
                           </a>
                        </li> -->
                     </ul>
                  </div>
               </div>
            </div>
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div id="portlet-config" class="modal hide">
               <div class="modal-header">
                  <button data-dismiss="modal" class="close" type="button"></button>
                  <h3>Widget Settings</h3>
               </div>
               <div class="modal-body"> Widget settings form goes here </div>
            </div>
            <div class="clearfix"></div>
              <div class="container m-b-50">
                 <div class="content">
                   @yield('content')
                 </div>
              </div>
              <div class="footer">
                <div class="container">
                  <div class="footer-content text-center">
                   <span>Copyright © 2015 Sutter Davis Hospital. All rights reserved.</span>
                  </div>
                </div>
              </div>
         </div>
         <!-- BEGIN CHAT --> 
         <!-- END CHAT -->
      </div>
      <!-- END CONTAINER -->
      <!-- BEGIN CORE JS FRAMEWORK--> 
      
      

      <script src="{{ asset('plugins/jquery-1.8.3.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/boostrapv3/js/bootstrap.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/breakpoints.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-unveil/jquery.unveil.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-block-ui/jqueryblockui.js') }}" type="text/javascript"></script>

      <script src="{{ asset('bower_components/parsleyjs/dist/parsley.js' ) }}" type="text/javascript"></script>
      <script src="{{ asset('bower_components/plupload/js/plupload.full.min.js' ) }}" type="text/javascript"></script>
      <script src="{{ asset('bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}" type="text/javascript"></script>
      
      <!-- END CORE JS FRAMEWORK -->
      <!-- BEGIN PAGE LEVEL JS -->
       <script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script> 
 
      <script src="{{ asset('plugins/pace/pace.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-scrollbar/jquery.scrollbar.min.js') }}" type="text/javascript"></script>    
      <script src="{{ asset('plugins/jquery-numberAnimate/jquery.animateNumbers.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-ricksaw-chart/js/d3.v2.js') }}"></script>
      <script src="{{ asset('plugins/jquery-ricksaw-chart/js/rickshaw.min.js') }}"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="{{ asset('plugins/jquery-morris-chart/js/morris.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.time.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.selection.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.animator.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.orderBars.js') }}"></script>
      <script src="{{ asset('plugins/jquery-sparkline/jquery-sparkline.js') }}"></script>
      <script src="{{ asset('plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js') }}"></script>
      <script src="{{ asset('plugins/dropzone/dropzone.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
      <script type="text/javascript" src="{{ asset('plugins/jquery-multiselect/jquery.multiselect.js') }}"></script>
      <!-- END PAGE LEVEL PLUGINS -->

      
      <!-- BEGIN CORE TEMPLATE JS -->
      <script src="{{ asset('js/core.js') }}" type="text/javascript"></script>

      <script src="{{ asset('js/demo.js') }}" type="text/javascript"></script>
      <script src="{{ asset('js/tabs_accordian.js') }}" type="text/javascript"></script>
      <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
      <!-- END CORE TEMPLATE JS --> 




   </body>
</html>