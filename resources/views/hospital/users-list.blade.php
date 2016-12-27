@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <!--li>
            <a href="{{ url( 'admin/' ) }}"><span>Home</span></a>
         </li-->
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Project Users</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="pull-right m-t-25">
                     @if(hasHospitalPermission($hospital['url_slug'],['edit']))
                     <a href="{{ url( $hospital['url_slug'].'/users/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</a>
                     @endif
                  </div>
<div class="page-title">
                     <h3><span class="semi-bold">Project Users</span></h3>
                      <p>(List User for {{ $hospital['name'] }})</p>
                  </div>
                  <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                           <table class="table table-hover" id="example" >
                              <thead>
                                 <tr>
                                    <th>Name</th>
                                    <th>Email ID</th>
                                    <th>Contact</th>
                                    <th>Access</th>
                                    <th>Projects assigned</th>
                                  </tr>
                              </thead>
                              <tbody>
                              @foreach($users as $user)
                                 <tr class="odd gradeX" onclick="window.document.location='{{ url( $hospital['url_slug'].'/users/'.$user['id'].'/edit' ) }}';">
                                    <td>{{ $user['name'] }}</td>
                                    <td>{{ $user['email'] }}</td>
                                    <td class="center">{{ $user['phone'] }}</td>
                                    <td class="center">
                                       @if($user['view'])
                                       <i class="fa fa-eye"></i>&nbsp;&nbsp;
                                       @endif
                                       
                                       @if($user['edit'])
                                          <i class="fa fa-pencil-square-o"></i>
                                       @endif
                                    </td>
                                    <td>
                                       @if($user['has_all_access'] == 'yes')
                                          <span>All</span>
                                       @else
                                          @if($countCheck[$user['id']] == 2)
                                             <span>{{ $mappingData[$user['id']] }}</span>   
                                          @else
                                             <a class="pop" href="javascript:void(0);" data-toggle="popover" data-placement="bottom" data-content="{{ $mappingData[$user['id']] }}" data-original-title="">Multiple</a>
                                          @endif
                                          
                                       @endif
                                   </td>
                                 </tr>
                              @endforeach
                                 
                             </tbody>
                           </table>
                        </div>
                     </div>

<script>
$(document).ready(function(e){
   $(".pop").popover({ trigger: "manual" , html: true, animation:false})
        .on("mouseenter", function () {
            var _this = this;
            $(this).popover("show");
            $(".popover").on("mouseleave", function () {
                $(_this).popover('hide');
            });
        }).on("mouseleave", function () {
        var _this = this;
        setTimeout(function () {
            if (!$(".popover:hover").length) {
                $(_this).popover("hide");
            }
        }, 300);
    });
});
</script>   
@endsection