@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Submission Notifications</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


<div class="clearfix m-b-15">
     <h3 class="pull-left"><span class="semi-bold">Submission Notifications</span> Report</h3>    
                     <!-- <p>(Click on any Patient ID to see Profile Details)</p> -->
                      <a class="btn btn-primary pull-right" id="btnSave" title="Download this page as a printable PDF"><i class="fa fa-print" style="color:#fff;"></i> Get PDF
     <span class="addLoader"></span></a>
                     <div class="patient-search pull-right m-r-15">
                       <form name="searchData" method="GET"> 
                       <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
                          <option class="ttuc" value="">-select patient-</option>
                           @foreach($allPatients as $patient)
                             <option class="ttuc patient-refer{{ $patient['reference_code'] }}"  value="{{ $patient['id'] }}">{{ $patient['reference_code'] }}</option>
                           @endforeach
                          </select> 
                     </form>
                    </div>
                  </div>
                  <div class="grid simple" id="page1">
                     <div class="grid-body no-border table-data">
                        <br>
          
                        <!-- Chart - Added -->
                        <div class="row">
                           <div class="col-md-9"></div>
                           <div class="col-md-3 text-right filter-dropdown submission-filter">
                              <span class="cf-loader hidden submissionFilter"></span>
                              <form method="get"> 
                              <label class="filter-label m-t-15 m-r-10">Filter</label>                              
                             <select name="reviewStatus" id="reviewStatus" class="pull-right select2 m-t-5 m-b-20 form-control inline filterby pull-right">
                                <option value="all">All</option>
                                <option {{ ($reviewStatus=='reviewed_no_action')?'selected':''}} value="reviewed_no_action">Reviewed - No action {{ ($project['reviewed_no_action'])?$project['reviewed_no_action']:'' }}</option>

                                <option {{ ($reviewStatus=='reviewed_call_done')?'selected':''}} value="reviewed_call_done">Reviewed - Call done {{ ($project['reviewed_call_done'])?$project['reviewed_call_done']:'' }}</option>

                                <option {{ ($reviewStatus=='reviewed_appointment_fixed')?'selected':''}} value="reviewed_appointment_fixed">Reviewed - Appointment fixed {{ ($project['reviewed_appointment_fixed'])?$project['reviewed_appointment_fixed']:'' }}</option>

                                <option {{ ($reviewStatus=='unreviewed')?'selected':'' }} value="unreviewed">Unreviewed {{ ($project['unreviewed'])?$project['unreviewed']:'' }}</option>
                                <!-- <option {{ ($reviewStatus=='missed')?'selected':'' }} value="missed">Missed</option> -->
                             </select>
                             
                             </form>
                             
                           </div>
                          <!-- <div class="col-md-3 text-right">
                               <input type="text" aria-controls="example" class="input-medium m-t-5" placeholder="search by patient id" style="    width: 100%;"> 
                           </div>-->
                        </div>
                        <div class="alert alert-info alert-black cust-alert">
                          
                           Submission Notifications Report
                          <sm class="light">(These are the notifications generated for submissions)</sm>
                        
                        </div>
                        <div class="grid-body no-border" style="display: block;padding: 10px 5px;">
                          <table class="table table-flip-scroll table-hover dashboard-tbl" cond-type="" cond="">
                          <thead class="cf">
                             <tr>
                                <th class="sorting" width="10%">Patient ID <br><br></th>
                                <th class="sorting "># Submission<br><br></th>
                                
                                <th class="sorting" width="35%">Reason<br><br>
                                </th>
                                <th class="sorting" width="35%">Review Note<br><br>
                                </th>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody id="submissionData" limit="5" object-type="submission" object-id="0">
                           
                          @if(!empty($submissionNotifications['alertMsg']))  
                           <?php
                                $firstBreak = 0;
                                $firstBreakCapture = 0;
                                $addClass = "";
                              ?>    
                              @foreach($submissionNotifications['alertMsg'] as $submissionNotification)
                                  <?php
                                  // pdf
                                    $firstBreak = $firstBreak + 1;
                                    if($firstBreakCapture == 0){
                                      if($firstBreak == 6){
                                         $addClass = "printPdfMargin"; 
                                         $firstBreakCapture = 1;
                                         $firstBreak = 0;
                                      }else{
                                          $addClass = "";
                                      }
                                    }else{
                                      if($firstBreak == 9){
                                         $addClass = "printPdfMarginE"; 
                                         $firstBreak = 0;
                                      }else{
                                          $addClass = "";
                                      }

                                    }
                                ?>
                                 <tr class="<?php echo $addClass; ?>" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/{{$submissionNotification['URL']}}';">
                                    <td class="text-center ttuc patient-refer{{ $submissionNotification['patient'] }}">{{ $submissionNotification['patient'] }}</td>
                                    <td class="text-center">
                                      <h4 class="semi-bold m-0 flagcount">{{ $submissionNotification['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submissionNotification['sequenceNumber'] }}</b></sm>
                                   </td>
                                   <td class="text-center text-success">{{ $submissionNotification['msg'] }}</td> 
                                   <td class="text-center"><div class="more">{{ $submissionNotification['reviewNote'] }}</div></td>
                                   <td class="text-center text-success">
                                   <!-- <div class="submissionStatus" @if(strlen($submissionNotification['reviewStatus']) >10 ) data-toggle="tooltip" @endif data-placement="top" title="{{ getStatusName($submissionNotification['reviewStatus']) }}">{{ getStatusName($submissionNotification['reviewStatus']) }}</div> -->
                                    <div class="submissionStatus" style="width: 100%;">{{ getStatusName($submissionNotification['reviewStatus']) }} {{ ($project[$submissionNotification['reviewStatus']])?$project[$submissionNotification['reviewStatus']]:'' }}</div>
                                   </td>
                                </tr>
                            @endforeach
                        @else 
                        <tr><td class="text-center no-data-found" colspan="20"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif    
                                
                          </tbody>
                       </table>
                       </div>
                        <hr style="    margin: 0px 0px 10px 0px;">
                     </div>
                  </div>
                  
<script type="text/javascript">
$(document).ready(function() {

      $('select[name="reviewStatus"]').change(function (event) { 
         $(".submissionFilter").removeClass('hidden');
         $('form').submit();
      });

      $('select[name="referenceCode"]').change(function (event) { 
        var referenceCode = $(this).val();
        if(referenceCode!='')
            window.location.href = BASEURL+"/patients/"+referenceCode; 
      });

   });

//pdf
   $(function() { 
      $("#btnSave").click(function() { 
      //convert all svg's to canvas
      $(".table tr.printPdfMargin td").addClass("print-pdf-marginSN");
      $(".table tr.printPdfMarginE td").addClass("print-pdf-marginSNE");
      $(".addLoader").addClass("cf-loader");

     var svgTags = document.querySelectorAll('#dashboardblock svg');
      for (var i=0; i<svgTags.length; i++) {
        var svgTag = svgTags[i];
        var c = document.createElement('canvas');
        c.width = svgTag.clientWidth;
        c.height = svgTag.clientHeight;
        svgTag.parentNode.insertBefore(c, svgTag);
        svgTag.parentNode.removeChild(svgTag);
        var div = document.createElement('div');
        div.appendChild(svgTag);
        canvg(c, div.innerHTML);
      }
      html2canvas($("#page1"), {
          background: '#FFFFFF',
              onrendered: function(canvas) {
                var imgData = canvas.toDataURL("image/jpeg", 1.0);  
                var imgWidth = 290; 
                var pageHeight = 225;  
                var imgHeight = canvas.height * imgWidth / canvas.width;
                var heightLeft = imgHeight;

                var doc = new jsPDF('l', 'mm');
                var position = 0;

                doc.addImage(imgData, 'JPEG', 3, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                  console.log(position)
                  position = heightLeft - imgHeight;
                  doc.addPage();
                  doc.addImage(imgData, 'JPEG', 3, position, imgWidth, imgHeight);
                  heightLeft -= pageHeight;
                }
                doc.save( 'Notifications Report.pdf');﻿
             }
          });
            setInterval(function(){ 
              $(".addLoader").removeClass("cf-loader"); 
                  $(".table tr.printPdfMargin td").removeClass("print-pdf-marginSN");
                  $(".table tr.printPdfMarginE td").removeClass("print-pdf-marginSNE");
            }, 3000);   
      });
    });

    // more-less
    $(document).ready(function() {
        // Configure/customize these variables.
        var showChar = 100;  // How many characters are shown by default
        var ellipsestext = "...";
        var moretext = "read more";
        var lesstext = "read less";
        

        $('.more').each(function() {
            var content = $(this).html();
     
            if(content.length > showChar) {
     
                var c = content.substr(0, showChar);
                var h = content.substr(showChar, content.length - showChar);
     
                var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink text-info">' + moretext + '</a></span>';
     
                $(this).html(html);
            }
     
        });
     
        $(".morelink").click(function(){
            if($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });
    });   
</script>   


@endsection

