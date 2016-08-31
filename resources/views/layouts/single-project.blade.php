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
      <meta name="useremail" content="{{ Auth::user()->email }}" />
      <meta name="userid" content="{{ Auth::user()->id }}" />
      <!-- BEGIN PLUGIN CSS -->
      <link href="{{ asset('project-admin-views/assets/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css" media="screen"/>
      <link href="{{ asset('project-admin-views/assets/plugins/bootstrap-select2/select2.css') }}" rel="stylesheet" type="text/css" media="screen"/>
      <!-- END PLUGIN CSS -->
      <!-- BEGIN CORE CSS FRAMEWORK -->
      <link href="{{ asset('project-admin-views/assets/plugins/boostrapv3/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('project-admin-views/assets/plugins/boostrapv3/css/bootstrap-theme.min.css') }}" rel="stylesheet" type="text/css"/>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
      <link href="{{ asset('project-admin-views/assets/plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('project-admin-views/assets/css/animate.min.css') }}" rel="stylesheet" type="text/css"/>
      <!-- END CORE CSS FRAMEWORK -->
      <link rel="stylesheet" type="text/css" href="{{ asset('project-admin-views/assets/plugins/bootstrap-datetime-picker/bootstrap-datetimepicker.css') }}" />
      <link rel="stylesheet" type="text/css" href="{{ asset('project-admin-views/assets/plugins/date-range-picker/daterangepicker.css') }}" />
      <link href="{{ asset('project-admin-views/assets/plugins/jquery-multiselect/jquery.multiselect.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('project-admin-views/assets/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
      <!-- BEGIN CSS TEMPLATE -->
      <link href="{{ asset('project-admin-views/assets/css/style.css') }}" rel="stylesheet" type="text/css"/>
      <link href="{{ asset('project-admin-views/assets/css/custom-icon-set.css') }}" rel="stylesheet" type="text/css"/>
      <link rel="stylesheet" type="text/css" href="{{ asset('project-admin-views/assets/plugins/jquery-nestable/jquery.nestable.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('project-admin-views/assets/plugins/switchery/switchery.min.css') }}">
   
      <!-- END CSS TEMPLATE -->
      <script src="{{ asset('bower_components/jquery/dist/jquery.js') }}"></script>
      <script src="{{ asset('bower_components/amcharts/dist/amcharts/amcharts.js') }}"></script>
      <script src="{{ asset('bower_components/amcharts/dist/amcharts/serial.js') }}"></script>
      <script src="{{ asset('bower_components/amcharts/dist/amcharts/pie.js') }}"></script> 
      <script src="{{ asset('bower_components/amcharts/dist/amcharts/themes/light.js') }}"></script>
      <script>
         var HOSPITAL_ID = 0;
         var PATIENT_ID = 0;
         var BASEURL = '{{ url() }}/{{ $hospital["url_slug"] }}/{{ $project["project_slug"] }}';
         var STARTDATE = '';
         var ENDDATE = '';
      </script>

      <!-- BEGIN TRACKJS -->
<!--       <script type="text/javascript">window._trackJs = { token: '21aaf9c6cfae433389d70210a39dabaf' };</script>
      <script type="text/javascript" src="https://d2zah9y47r7bi2.cloudfront.net/releases/current/tracker.js"></script> -->
      <!-- END TRACKJS -->
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
               <a href="{{ url() }}/{{ $hospital['url_slug'] }}">{{ hospitalImageExist($hospital) }}</a>
               <!-- END LOGO --> 
               <ul class="nav pull-right notifcation-center">
                  <li class="dropdown" id="header_task_bar">
                     <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/' ) }}" class="dropdown-toggle active" data-toggle="">
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
               <div class="pull-right">
                  <div class="chat-toggler">
                     <a href="#" class="pull-right">
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
                           <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/changepassword' ) }}"> Change Password</a>
                           </li>
                           <li class="divider"></li>
                           <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/logout' ) }}"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
                        </ul>
                     </li>
                  </ul>
               </div>
               <div class="pull-left">
               <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/' ) }}" class="inline">{{ hospitalImageExist($hospital) }}
               &nbsp;<h4 class="text-left semi-bold inline m-t-13" >{{ $project['name']}}</h4></a>
               </div>
               <!-- END TOP NAVIGATION MENU -->
               <!-- BEGIN CHAT TOGGLER -->
               
               <!-- END CHAT TOGGLER -->
            </div>

         </div>
         <!-- END TOP NAVIGATION BAR --> 
      <div class="site-nav">
         <div class="bar">
               <div class="container">
                  <div class="bar-inner">
                     <ul>
                        <li class="{{ ( $active_menu == 'dashbord')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/dashboard/' ) }}">
                           <span><i class="fa fa-tachometer"></i> Dashboard </span>
                           </a>
                        </li>
   
                        <li class="{{ ( $active_menu == 'patients')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/patients/' ) }}">
                           <span><i class="fa fa-wheelchair"></i> Patients </span>
                           </a>
                        </li>
        
                        <li class="{{ ( $active_menu == 'submission')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/submissions/' ) }}">
                           <span><i class="fa fa-list-alt"></i> Submissions </span>
                           </a>
                            
                        </li>
                        <li class="{{ ( $active_menu == 'submission-notification')? 'active-item' : ''}}">
                            
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/submission-notifications/' ) }}"><span><i class="fa fa-list-alt"></i> Notifications Report </span></a>
                            
                        </li>
                        <li class="{{ ( $active_menu == 'flags')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/flags/' ) }}">
                           <span><i class="fa fa-flag"></i> Flags </span>
                           </a>
                        </li>
                        <li class="{{ ( $active_menu == 'reports')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/reports/' ) }}">
                           <span><i class="fa fa-bar-chart"></i> Reports </span>
                           </a>
                        </li>
                        {{--@if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))--}}
                        <li class="classic {{ ( $active_menu == 'settings')? 'active-item' : ''}}">
                        <a href="javascript:;">
                          <span><i class="fa fa-cogs"></i> Settings</span> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="classic">
                        <!-- <li>
                          <a href="questionnaire.html">Questionnaire</a>
                        </li> -->
                        <li>
                          <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}">Questionnaire Setting</a>
                        </li>
                        <?php 
                        $questionnairedata = getQuestionnaireData($project["id"]); 
                        ?>
                        @if(!empty($questionnairedata) && $questionnairedata['status']!='published')
							 @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
								<li>
								  <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnairedata['questionnaireId'] ) }}">Configure Questionnaire</a>
								</li>
							 @endif
                        @elseif(!empty($questionnairedata) && $questionnairedata['status']=='published')
                        <li>
                          <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questions-summary/'.$questionnairedata['questionnaireId'] ) }}">Questionnaire Summary</a>
                        </li>
                        @endif
                        <li>
                          <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/alert-setting/' ) }}">Alert Setting</a>
                        </li>
                        <li>
                          <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/review-mapping/' ) }}">Review Mapping</a>
                        </li>
                        <!--  <li>
                            <a href="index.html">Message & Notifications
                              <span class="description">
                                Alerts help to gain user attention and 
                                give...
                              </spam>
                            </a>
                          </li> -->
                        </ul>
                      </li>
                      {{--@endif--}}
                     </ul>
                  </div>
               </div>
            </div>
      </div>

      </div>
      <!-- END HEADER -->
      <!-- BEGIN CONTAINER -->
      <div class="page-container row-fluid">
         <!-- BEGIN PAGE CONTAINER-->
         <div class="page-content">
            <!-- <div class="bar">
               <div class="container">
                  <div class="bar-inner">
                     <ul>
                        <li class="{{ ( $active_menu == 'dashbord')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/dashboard/' ) }}">
                           <span><i class="fa fa-tachometer"></i> Dashboard </span>
                           </a>
                        </li>
   
                        <li class="{{ ( $active_menu == 'patients')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/patients/' ) }}">
                           <span><i class="fa fa-wheelchair"></i> Patients </span>
                           </a>
                        </li>
        
                        <li class="{{ ( $active_menu == 'submission')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/submissions/' ) }}">
                           <span><i class="fa fa-list-alt"></i> Submissions </span>
                           </a>
                        </li>
                        <li class="{{ ( $active_menu == 'flags')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/flags/' ) }}">
                           <span><i class="fa fa-flag"></i> Flags </span>
                           </a>
                        </li>
                        <li class="{{ ( $active_menu == 'reports')? 'active-item' : ''}}">
                           <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/reports/' ) }}">
                           <span><i class="fa fa-bar-chart"></i> Reports </span>
                           </a>
                        </li>
                        @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
                        <li class="classic {{ ( $active_menu == 'settings')? 'active-item' : ''}}">
                        <a href="javascript:;">
                          <span><i class="fa fa-cogs"></i> Settings</span> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="classic">
                        
                        <li>
                          <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}">Questionnaire Setting</a>
                        </li>
                        
                        </ul>
                      </li>
                      @endif
                     </ul>
                  </div>
               </div>
            </div> -->
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
                 <div class="content" id="dashboardblock">
                   @yield('breadcrumb')
                   @yield('content')
                 </div>
              </div>
              <div class="footer">
                <div class="container">
                  <div class="footer-content text-center">
                   <span>Copyright © 2016 {{ $hospital['name'] }}. All rights reserved.</span>
                  </div>
                </div>
              </div>
         </div>
         <!-- BEGIN CHAT --> 
         <!-- END CHAT -->
      </div>
      <!-- END CONTAINER -->
      <!-- BEGIN CORE JS FRAMEWORK--> 
      <script type="text/javascript">
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
      })

      $(function() {

         function cb(start, end) {

            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('input[name="startDate"]').val(start.format('DD-MM-YYYY'));
            $('input[name="endDate"]').val(end.format('DD-MM-YYYY'));
            
            if($('input[name="flag"]').val()==1)
            {
               $('#reportrange').append('<span class="cf-loader"></span>');
               $('#reportrange span').closest('form').submit();
            }
            $('input[name="flag"]').val(1)
         }
 
          cb(moment(STARTDATE), moment(ENDDATE));
 

         
      $('#reportrange').daterangepicker({
         "ranges": {
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                  'Last 6 Months': [moment().subtract(6, 'months'), moment()],
                  'Last 12 Months': [moment().subtract(12, 'months'), moment()],
                    
                 },
         "startDate": moment(STARTDATE),
         "endDate":  moment(ENDDATE)
      }, cb);
 
      });
   </script>
       
      <script src="{{ asset('plugins/jquery-1.8.3.min.js') }}" type="text/javascript"></script>

      <script src="{{ asset('bower_components/parsleyjs/dist/parsley.js' ) }}" type="text/javascript"></script>
      <script src="{{ asset('bower_components/plupload/js/plupload.full.min.js' ) }}" type="text/javascript"></script>
      <script src="{{ asset('bower_components/underscore/underscore-min.js') }}" type="text/javascript"></script>
       <!--script src="{{ asset('bower_components/jquery-confirm/jquery.confirm.min.js') }}" type="text/javascript"></script-->
      
      
      <script src="{{ asset('plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/boostrapv3/js/bootstrap.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/breakpoints.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-unveil/jquery.unveil.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-block-ui/jqueryblockui.js') }}" type="text/javascript"></script>
      <!-- END CORE JS FRAMEWORK -->
      <!-- BEGIN PAGE LEVEL JS -->
      
      <script src="{{ asset('project-admin-views/assets/js/bootstrap-select.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/pace/pace.min.js') }}" type="text/javascript"></script>
      <script src="{{ asset('plugins/jquery-scrollbar/jquery.scrollbar.min.js') }}" type="text/javascript"></script>    
      <script src="{{ asset('plugins/jquery-numberAnimate/jquery.animateNumbers.js') }}" type="text/javascript"></script>


      <script src="{{ asset('plugins/bootstrap-select2/select2.min.js') }}" type="text/javascript"></script>
      <script type="text/javascript" src="{{ asset('plugins/jquery-multiselect/jquery.multiselect.js') }}"></script>
      <!-- END PAGE LEVEL PLUGINS -->

       <script src="{{ asset('project-admin-views/assets/plugins/date-range-picker/moment.js') }}"></script>
      <script src="{{ asset('project-admin-views/assets/plugins/date-range-picker/daterangepicker.js') }}" type="text/javascript"></script>
      <script src="{{ asset('project-admin-views/assets/plugins/bootstrap-datetime-picker/bootstrap-datetimepicker.js') }}" type="text/javascript"></script>
   
      <script type='text/javascript' src="{{ asset('project-admin-views/assets/js/common.js') }}"></script>
      <script type='text/javascript' src="{{ asset('project-admin-views/assets/js/css.js') }}"></script>

       <script type="text/javascript" src="{{ asset('project-admin-views/assets/plugins/jquery-nestable/jquery.nestable.min.js') }}"></script>
       <script src="{{ asset('project-admin-views/assets/plugins/switchery/switchery.min.js') }}" type="text/javascript"></script>

      <!-- html to pdf -->
      <script type='text/javascript' src="{{ asset('js/htmltopdf/canvg.js') }}"></script>
      <script type='text/javascript' src="{{ asset('js/htmltopdf/rgbcolor.js') }}"></script>
      <script type='text/javascript' src="{{ asset('js/htmltopdf/html2canvas.js') }}"></script>
      <script type='text/javascript' src="{{ asset('js/htmltopdf/jspdf.js') }}"></script>
      <!-- html to pdf -->
      <!-- BEGIN CORE TEMPLATE JS -->
      <script src="{{ asset('js/core.js') }}" type="text/javascript"></script>

      <script src="{{ asset('js/demo.js') }}" type="text/javascript"></script>
      <script src="{{ asset('js/tabs_accordian.js') }}" type="text/javascript"></script>
      <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
      <!-- END CORE TEMPLATE JS --> 


 
</html>