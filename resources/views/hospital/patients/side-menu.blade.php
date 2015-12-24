<ul class="nav nav-tabs inner-tabs" id="tab-{{ $tab }}">
      <li class="{{ ( $active_tab == 'summary')? 'active' : ''}}"><a href="{{ url($hospital['url_slug'].'/patients/'.$patient['id']) }}"><i class="fa fa-wheelchair"></i> Summary</a></li>
      <li class="{{ ( $active_tab == 'users')? 'active' : ''}}"><a href="#"><i class="fa fa-list-alt"></i> Submissions</a></li>
      <li class="{{ ( $active_tab == 'users')? 'active' : ''}}"><a href="#"><i class="fa fa-bar-chart"></i> Baseline Score</a></li>
      <li class="{{ ( $active_tab == 'reports')? 'active' : ''}}"><a href="{{ url($hospital['url_slug'].'/patients/'.$patient['id'].'/submission-reports') }}"><i class="fa fa-bar-chart"></i> Reports</a></li>
      <li class="{{ ( $active_tab == 'users')? 'active' : ''}}"><a href="#"><i class="fa fa-bar-chart"></i> Details</a></li>
</ul>