@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Hospital Users</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="pull-right m-t-25">
                     <!-- <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a> -->
                     <a href="{{ url( 'admin/users/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</a>
                  </div>
<div class="page-title">
                     <h3><span class="semi-bold">Hospital Users</span></h3>
                      <p>(Users on Mylan)</p>
                  </div>
                  <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                           <table class="table table-hover blur" id="example" >
                              <thead>
                                 <tr>
                                    <th>Name</th>
                                    <th>Email ID</th>
                                    <th>Contact</th>
                                    <th>Permissions</th>
                                    <th>Hospitals assigned</th>
                                  </tr>
                              </thead>
                              <tbody>
                              @foreach($users as $user)
                                 <tr class="odd gradeX" onclick="window.document.location='{{ url( 'admin/users/'.$user['id'].'/edit' ) }}';">
                                    <td><strong>{{ $user['name'] }}</strong></td>
                                    <td>{{ $user['email'] }}</td>
                                    <td class="center">{{ $user['phone'] }}</td>
                                    <td class="center">
                                    @if($user['view'])
                                       <a href="javascript:void(0)" class="ttip m-r-15" data-toggle="tooltip" data-placement="right" title="Can veiw"><i class="fa fa-eye"></i></a>
                                    @endif
                                    
                                    @if($user['edit'])
                                       <a href="javascript:void(0)" class="ttip" data-toggle="tooltip" data-placement="right" title="Can edit"><i class="fa fa-pencil-square-o"></i></a>
                                    @endif
                                      </td>
                                      <td>
                                          @if($user['has_all_access'] == 'yes')
                                             <span>All</span>
                                          @else
                                             @if($countCheck[$user['id']] == 2)
                                                <span>{{ $relatedHospitals[$user['id']] }}</span>   
                                             @else
                                                <a class="pop" href="javascript:void(0);" data-toggle="popover" data-placement="bottom" data-content="{{ $relatedHospitals[$user['id']] }}" data-original-title="">Multiple</a>
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