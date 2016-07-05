@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="#" class="active">Projects</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


    <div class="pull-right m-t-10">
                     @if(hasHospitalPermission($hospital['url_slug'],['edit']))
                     <a href="{{ url( $hospital['url_slug'].'/projects/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Project</a>
                     @endif
                  </div>
                  <div class="page-title">
                     <h3 class="m-b-0"><span class="semi-bold">Projects</span></h3>
                     <p>(Showing all Projects under {{ $hospital['name'] }})</p>
                  </div>
                  <div class="grid simple">
                     <div class="grid-body no-border">
                        <br>
                        @if(!empty($projects))
                        @foreach($projects as $project)
                        <div>
                           <div class="pull-right">
                              <a target="_blank" href="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}?login=project" ><button class="btn btn-default btn-small m-r-15">Login as {{$project['name']}}</button></a>
                              <!-- <span class="text-danger"><i class="fa fa-flag"></i> 5 New</span><span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                              <span class="text-warning"><i class="fa fa-flag"></i> 5 New</span> -->
                           </div>
                           <a href="{{ url( $hospital['url_slug'].'/projects/'. $project['id'] .'/edit' ) }}">
                              <h3><span class="semi-bold">{{$project['name']}}</span></h3>
                           </a>
                        </div>
                        <br>
                        <em>{{$project['description']}}</em>
                        
                        <br>
                        <hr>
                        @endforeach
                        @else
                           <div>No Project Added Yet</div>
                        @endif
                        
                     </div>
                  </div>

@endsection