<ul class="nav nav-tabs inner-tabs" id="tab-{{ $tab }}">
      <li class="{{ ( $active_tab == 'summary')? 'active' : ''}}" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id']) }}';"><a href="#"><i class="fa fa-wheelchair"></i> Summary</a></li>
      <li class="{{ ( $active_tab == 'submissions')? 'active' : ''}}" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/submissions') }}';"><a href="#"><i class="fa fa-list-alt"></i> Submissions</a></li>

      <li class="{{ ( $active_tab == 'flags')? 'active' : ''}}" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/flags') }}';"><a href="#"><i class="fa fa-bar-chart"></i> Flags</a></li>
      <li class="{{ ( $active_tab == 'base_line')? 'active' : ''}}" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/base-line-score/list') }}';"><a href="#"><i class="fa fa-bar-chart"></i> Baseline Score</a></li>
      <li class="{{ ( $active_tab == 'reports')? 'active' : ''}}" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/patient-reports') }}';"><a href="#"><i class="fa fa-bar-chart"></i> Reports</a></li>
      <!-- <li class="{{ ( $active_tab == 'users')? 'active' : ''}}"><a href="#"><i class="fa fa-bar-chart"></i> Details</a></li> -->
      <li class="{{ ( $active_tab == 'submissions-notification')? 'active' : ''}}" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/submission-notifications') }}';"><a href="#"><i class="fa fa-list-alt"></i> Submission Notifications</a></li>
	  @if($userdevice)
		  @if($userdevice == 'yes')
		  <li class="{{ ( $active_tab == 'user-devices')? 'active' : ''}}" onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/patient-devices') }}';"><a href="#"><i class="fa fa-list-alt"></i> Setup Devices</a></li>
		  @endif
	  @endif	  
</ul>