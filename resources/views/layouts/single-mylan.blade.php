<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
      <meta charset="utf-8" />
      <title>Mylan - Hospital Administrator</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
      <meta content="" name="description" />
      <meta content="" name="author" />
      <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" type="image/png" href="{{ asset('images/others/favicon.ico') }}">
      <!-- BEGIN PLUGIN CSS -->
      <link href="{{ asset('plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css" media="screen"/>
      <link href="{{ asset('plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
      <link rel="stylesheet" href="{{ asset('plugins/jquery-ricksaw-chart/css/rickshaw.css') }}" type="text/css" media="screen">
      <link rel="stylesheet" href="{{ asset('plugins/jquery-morris-chart/css/morris.css') }}" type="text/css" media="screen">
      <!-- END PLUGIN CSS -->
      <!-- BEGIN CORE CSS FRAMEWORK -->
      <link href="{{ asset('plugins/boostrapv3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('plugins/boostrapv3/css/bootstrap-theme.min.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('css/animate.min.css') }}" rel="stylesheet" type="text/css"/>
      <!-- END CORE CSS FRAMEWORK -->
      <!-- BEGIN CSS TEMPLATE -->
      <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('css/custom-icon-set.css') }}" rel="stylesheet" type="text/css"/>
      <!-- END CSS TEMPLATE -->
      <script>
         var HOSPITAL_ID = 0;
         var PATIENT_ID = 0;
         var BASEURL = '{{ url() }}/admin';
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
               <a href="{{ url('admin/' ) }}"><img src="{{ asset('img/mylan-logo.png') }}" class="logo" alt=""  data-src="{{ asset('img/mylan-logo.png') }}" data-src-retina="{{ asset('img/mylan-logo.png') }}" width="106" height="21"/></a>
               <!-- END LOGO --> 
               <ul class="nav pull-right notifcation-center">
                  <li class="dropdown" id="header_task_bar">
                     <a href="{{ url('admin/' ) }}" class="dropdown-toggle active" data-toggle="">
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
               <a href="{{ url('admin/' ) }}" class="pull-left"><img src="{{ asset('img/mylan-logo.png') }}" class="logo" alt=""  data-src="{{ asset('img/mylan-logo.png') }}" width="auto" height="40"/></a>
               <!-- END TOP NAVIGATION MENU -->
               <!-- BEGIN CHAT TOGGLER -->
               <div class="pull-right">
                  <div class="chat-toggler">
                     <a href="#">
                        <div class="user-details">
                           <div class="username">
                              {{ Auth::user()->name }}    
                              <span class="badge badge-default">Hospital Admin</span>               
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
                           <li><a href="{{ url('admin/logout' ) }}"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
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
                        <li class="{{ ( $active_menu == 'dashbord')? 'active-item' : ''}}">
                           <a href="{{ url( 'admin/dashboard/' ) }}">
                           <span><i class="fa fa-tachometer"></i> Dashboard </span>
                           </a>
                        </li>
                        <li class="{{ ( $active_menu == 'hospital')? 'active-item' : ''}}">
                           <a href="{{ url( 'admin/hospitals/' ) }}">
                           <span><i class="fa fa-hospital-o"></i> Hospitals </span>
                           </a>
                        </li>
                        
                        <li class="{{ ( $active_menu == 'users')? 'active-item' : ''}}">
                           <a href="{{ url( 'admin/users/' ) }}">
                           <span><i class="fa fa-users"></i> Users </span>
                           </a>
                        </li>
                        <!-- <li>
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
                   @yield('breadcrumb')
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
      <script>
            var BASEURL = '{{ url() }}';
      </script>
      <!-- END CONTAINER -->
      <!-- BEGIN CORE JS FRAMEWORK--> 
      <script src="{{ asset('plugins/jquery-1.8.3.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/boostrapv3/js/bootstrap.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/breakpoints.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-unveil/jquery.unveil.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-block-ui/jqueryblockui.js') }}" type="text/javascript"></script>
      <!-- END CORE JS FRAMEWORK -->
      <!-- BEGIN PAGE LEVEL JS -->
       
      <script src="{{ asset('plugins/pace/pace.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-scrollbar/jquery.scrollbar.min.js') }}" type="text/javascript"></script>    
      <script src="{{ asset('plugins/jquery-numberAnimate/jquery.animateNumbers.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-ricksaw-chart/js/d3.v2.js') }}"></script>
      <script src="{{ asset('plugins/jquery-ricksaw-chart/js/rickshaw.min.js') }}"></script>
 
      <script src="{{ asset('plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.time.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.selection.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.animator.min.js') }}"></script>
      <script src="{{ asset('plugins/jquery-flot/jquery.flot.orderBars.js') }}"></script>
      <script src="{{ asset('plugins/jquery-sparkline/jquery-sparkline.js') }}"></script>
      <script src="{{ asset('plugins/jquery-easy-pie-chart/js/jquery.easypiechart.min.js') }}"></script>
      <script src="{{ asset('plugins/dropzone/dropzone.js') }}" type="text/javascript"></script>

      <script src="{{ asset('bower_components/parsleyjs/dist/parsley.js' ) }}" type="text/javascript"></script>
      <script src="{{ asset('bower_components/plupload/js/plupload.full.min.js' ) }}" type="text/javascript"></script>
      <!-- END PAGE LEVEL PLUGINS -->
 
      <!-- BEGIN CORE TEMPLATE JS -->
      <script src="{{ asset('js/core.js') }}" type="text/javascript"></script>
      <script src="{{ asset('js/chat.js') }}" type="text/javascript"></script>
      <script src="{{ asset('js/demo.js') }}" type="text/javascript"></script>
      <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
      <!-- END CORE TEMPLATE JS --> 
   </body>
</html>