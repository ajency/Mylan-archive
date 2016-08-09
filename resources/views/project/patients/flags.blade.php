@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
      <ul class="breadcrumb">

        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" class="ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
        <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Flags</a> </li>
         

      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<a class="btn btn-primary pull-right" id="btnSave" title="Download this page as a printable PDF"><i class="fa fa-print"></i> Get PDF
<span class="addLoader"></span></a>
   <div class="pull-right m-r-15">
    <form name="searchData" method="GET"> 
       <input type="hidden" class="form-control" name="startDate"  >
      <input type="hidden" class="form-control" name="endDate"  >
      <input type="hidden" class="form-control" name="type"  value="{{ $filterType }}" >
        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
           <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
           <span></span> <b class="caret"></b>
        </div>

  </form>
   <input type="hidden" name="flag" value="0">
  </div>
  <div class="m-r-15 pull-right patient-search">
<select class="selectpicker" data-live-search="true" title="Patient" name="referenceCode">
      <option class="ttuc" value="">-select patient-</option>
       @foreach($allPatients as $patientData)
         <option class="ttuc patient-refer{{ $patientData['reference_code'] }}" {{($patient['reference_code']==$patientData['reference_code'])?'selected':''}}  value="{{ $patientData['id'] }}">{{ $patientData['reference_code'] }}</option>
       @endforeach
      </select> 
</div>
<div class="page-title">
     <h3>Patient <span class="semi-bold ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>
  </div>
 <div class="tabbable tabs-left" id="page1">
                      @include('project.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data" id="Submissions">
   
                        </div>
                        <div class="tab-pane table-data active" id="Flags">
                                     <ul class="nav nav-tabs" role="tablist">
                              <li role="presentation" class="active"><a href="#all" aria-controls="all" role="tab" data-toggle="tab">All Flags  <i class="fa fa-flag text-muted"></i></a>
                              </li>
                              <li role="presentation" ><a href="#red" aria-controls="red" role="tab" data-toggle="tab">Red <i class="fa fa-flag text-error"></i></a>
                              </li>
                              <li role="presentation"><a href="#amber" aria-controls="amber" role="tab" data-toggle="tab">Amber <i class="fa fa-flag text-warning"></i></a></li>
                              <li role="presentation"><a href="#green" aria-controls="green" role="tab" data-toggle="tab">Green <i class="fa fa-flag text-success"></i></a></li>
                           </ul>
                           <!-- Tab panes -->
                           <div class="tab-content">
                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3 text-right filter-dropdown m-t-15 submission-filter">
                                     <span class="cf-loader hidden flagsFilter pull-right"></span>
                                     <form name="filterData" method="get" class="pull-right">
                                     <label class="filter-label m-t-15 m-r-10">Filter</label>  
                                      <input type="hidden" class="form-control" name="startDate" value="{{ $startDate }}"  >
                                      <input type="hidden" class="form-control" name="endDate" value="{{ $endDate }}" >
                                       <select name="type" id="type" class=" select2  form-control inline filterby ">
                                          <option value="">All</option>
                                          <option {{ ($filterType=='previous')?'selected':''}} value="previous">Previous</option>
                                          <option {{ ($filterType=='baseline')?'selected':''}} value="baseline">Baseline</option>
                                       </select>                                       
                                       </form>
                                       
                                    </div>
                                  
                                 </div>
                                 <hr class="">
                              <div role="tabpanel" class="tab-pane active" id="all">
                                  
                    <table class="table table-hover dashboard-tbl">
                      <thead>
                         <tr>
                           
                            <th width="20%"># Submission</th>
                            <th>Reason for Flag</th>
                            <th>Type</th>
                         </tr>
                      </thead>
                      <tbody>
                      @if(!empty($submissionFlags['all']))  
						<?php
							$totalcount =0 ;
							$counter = 0;
              //pdf
              $firstBreak = 0;
              $firstBreakCapture = 0;
              $addClass = "";
						?>
                        @foreach($submissionFlags['all'] as $allSubmissionFlag)
                         <?php 
							$totalcount = $totalcount + 1; 
							if($allSubmissionFlag['flag'] == ""){
								 $counter = $counter+1;
							}
                          if($allSubmissionFlag['flag']=='no_colour' || $allSubmissionFlag['flag']=='')
                               continue;
                          ?>
                          <?php
                              //pdf
                              $firstBreak = $firstBreak +1;
                              if($firstBreakCapture == 0){
                                if($firstBreak == 22){
                                   $addClass = "printPdfMargin"; 
                                   $firstBreakCapture = 1;
                                   $firstBreak = 0;
                                }else{
                                    $addClass = "";
                                }
                              }else{
                                if($firstBreak == 24){
                                  if($firstBreakCapture > 9 and $firstBreakCapture < 15){
                                     $addClass = "printPdfMarginLongData"; 
                                  }else if($firstBreakCapture < 9){
                                    $addClass = "printPdfMarginSecond"; 
                                  }else{
                                    $addClass = "printPdfMarginSecond";
                                  } 
                                   $firstBreak = 0;
                                   $firstBreakCapture = $firstBreakCapture + 1;
                                }else{
                                    $addClass = "";
                                }

                              }
                            ?>
                         <tr class="odd gradeX <?php echo $addClass; ?>" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $allSubmissionFlag['responseId'] }}';">
                            <td width="110px">
                               <div class="p-l-10 p-r-20">
                                  <h4 class="semi-bold m-0 flagcount">{{ $allSubmissionFlag['date'] }}</h4>
                                  <sm>#{{ $allSubmissionFlag['sequenceNumber'] }}</sm>
                               </div>
                            </td>
                            <td><?php echo  $allSubmissionFlag['reason'] ?></td>
                            <td><i class="fa fa-flag text-{{ $allSubmissionFlag['flag'] }}"></i></td>
                         </tr>
                        @endforeach 
						<?php
							if($totalcount == $counter){
								echo '<tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>';
							}
						?>
                      @else 
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif       
                      </tbody>
                   </table>
                     
                              </div>
                                       <div role="tabpanel" class="tab-pane " id="red">
               
                    <table class="table table-hover dashboard-tbl">
                      <thead>
                         <tr>
                           
                            <th width="20%"># Submission</th>
                            <th>Reason for Flag</th>
                            <th>Type</th>
                         </tr>
                      </thead>
                    <tbody>
                    @if(!empty($submissionFlags['flags']['red'])) 
                    <?php
                          //pdf
                          $firstBreak = 0;
                          $firstBreakCapture = 0;
                          $addClass = "";
                        ?> 
                       @foreach($submissionFlags['flags']['red'] as $submissionFlag)
                        <?php
                              //pdf
                              $firstBreak = $firstBreak +1;
                              if($firstBreakCapture == 0){
                                if($firstBreak == 22){
                                   $addClass = "printPdfMargin"; 
                                   $firstBreakCapture = 1;
                                   $firstBreak = 0;
                                }else{
                                    $addClass = "";
                                }
                              }else{
                                if($firstBreak == 24){
                                  if($firstBreakCapture > 9 and $firstBreakCapture < 15){
                                     $addClass = "printPdfMarginLongData"; 
                                  }else if($firstBreakCapture < 9){
                                    $addClass = "printPdfMarginSecond"; 
                                  }else{
                                    $addClass = "printPdfMarginSecond";
                                  } 
                                   $firstBreak = 0;
                                   $firstBreakCapture = $firstBreakCapture + 1;
                                }else{
                                    $addClass = "";
                                }

                              }
                            ?>
                       <tr class="odd gradeX <?php echo $addClass; ?>" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">
                          <td width="110px">
                             <div class="p-l-10 p-r-20">
                                <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                                <sm>#{{ $submissionFlag['sequenceNumber'] }}</sm>
                             </div>
                          </td>
                          <td><?php echo $submissionFlag['reason'] ?></td>
                          <td><i class="fa fa-flag text-{{ $submissionFlag['flag'] }}"></i></td>
                       </tr>
                      @endforeach 
                    @else 
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif      
                    </tbody>
                 </table>
                       
                              </div>
                                      <div role="tabpanel" class="tab-pane" id="amber">
                           
                                 <table class="table table-hover">
                                    <thead>
                                       <tr>
                                          <th width="20%"># Submission</th>
                                          <th>Reason for Flag</th>
                                          <th>Type</th>
                                       </tr>
                                    </thead>
                                  <tbody>
                                  @if(!empty($submissionFlags['flags']['amber'])) 
                                   <?php
                                      //pdf
                                      $firstBreak = 0;
                                      $firstBreakCapture = 0;
                                      $addClass = "";
                                    ?>  
                                     @foreach($submissionFlags['flags']['amber'] as $submissionFlag)
                                      <?php
                                        //pdf
                                        $firstBreak = $firstBreak +1;
                                        if($firstBreakCapture == 0){
                                          if($firstBreak == 22){
                                             $addClass = "printPdfMargin"; 
                                             $firstBreakCapture = 1;
                                             $firstBreak = 0;
                                          }else{
                                              $addClass = "";
                                          }
                                        }else{
                                          if($firstBreak == 24){
                                            if($firstBreakCapture > 9 and $firstBreakCapture < 15){
                                               $addClass = "printPdfMarginLongData"; 
                                            }else if($firstBreakCapture < 9){
                                              $addClass = "printPdfMarginSecond"; 
                                            }else{
                                              $addClass = "printPdfMarginSecond";
                                            } 
                                             $firstBreak = 0;
                                             $firstBreakCapture = $firstBreakCapture + 1;
                                          }else{
                                              $addClass = "";
                                          }

                                        }
                                      ?>
                                     <tr class="odd gradeX <?php echo $addClass; ?>" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">
                                        <td width="110px">
                                           <div class="p-l-10 p-r-20">
                                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                                              <sm>#{{ $submissionFlag['sequenceNumber'] }}</sm>
                                           </div>
                                        </td>
                                        <td><?php echo $submissionFlag['reason'] ?></td>
                                        <td><i class="fa fa-flag text-{{ $submissionFlag['flag'] }}"></i></td>
                                     </tr>
                                    @endforeach 
                                 @else 
                                  <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                                @endif       
                                  </tbody>
                               </table>
                             
                              </div>
                              <div role="tabpanel" class="tab-pane" id="green">
                       
                                 <table class="table table-hover">
                                    <thead>
                                       <tr>
                                          <th width="20%"># Submission</th>
                                          <th>Reason for Flag</th>
                                          <th>Type</th>
                                       </tr>
                                    </thead>
                                  <tbody>
                                  @if(!empty($submissionFlags['flags']['green']))  
                                  <?php
                                      //pdf
                                      $firstBreak = 0;
                                      $firstBreakCapture = 0;
                                      $addClass = "";
                                    ?>  
                                     @foreach($submissionFlags['flags']['green'] as $submissionFlag)
                                      <?php
                                        //pdf
                                        $firstBreak = $firstBreak +1;
                                        if($firstBreakCapture == 0){
                                          if($firstBreak == 22){
                                             $addClass = "printPdfMargin"; 
                                             $firstBreakCapture = 1;
                                             $firstBreak = 0;
                                          }else{
                                              $addClass = "";
                                          }
                                        }else{
                                          if($firstBreak == 24){
                                            if($firstBreakCapture > 9 and $firstBreakCapture < 15){
                                               $addClass = "printPdfMarginLongData"; 
                                            }else if($firstBreakCapture < 9){
                                              $addClass = "printPdfMarginSecond"; 
                                            }else{
                                              $addClass = "printPdfMarginSecond";
                                            } 
                                             $firstBreak = 0;
                                             $firstBreakCapture = $firstBreakCapture + 1;
                                          }else{
                                              $addClass = "";
                                          }

                                        }
                                      ?>
                                     <tr class="odd gradeX <?php echo $addClass; ?>" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $submissionFlag['responseId'] }}';">  
                                        <td width="110px">
                                           <div class="p-l-10 p-r-20">
                                              <h4 class="semi-bold m-0 flagcount">{{ $submissionFlag['date'] }}</h4>
                                              <sm>#{{ $submissionFlag['sequenceNumber'] }}</sm>
                                           </div>
                                        </td>
                                        <td><?php echo $submissionFlag['reason'] ?></td>
                                        <td><i class="fa fa-flag text-{{ $submissionFlag['flag'] }}"></i></td>
                                     </tr>
                                    @endforeach 
                                  @else 
                                   <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                                   @endif      
                                  </tbody>
                               </table>
                             
                              </div>
                        </div>
 
                           
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>

<script type="text/javascript">
var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

   $(document).ready(function() {

      $('select[name="type"]').change(function (event) { 
        $(".flagsFilter").removeClass('hidden');
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
     $(".table tr.printPdfMargin td").addClass("print-pdf-margin-set-flags");
     $(".table tr.printPdfMarginSecond td").addClass("print-pdf-margin-flags-extra");
     $(".table tr.printPdfMarginLongData td").addClass("print-pdf-margin-large-flags");
     
     $(".addLoader").addClass("cf-loader");
     $("#page1").css("background","#FFFFFF");

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
                var imgWidth = 210; 
                var pageHeight = 295;  
                var imgHeight = canvas.height * imgWidth / canvas.width;
                var heightLeft = imgHeight;

                var doc = new jsPDF('p', 'mm');
                var position = 0;

                doc.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                  position = heightLeft - imgHeight;
                  doc.addPage();
                  doc.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
                  heightLeft -= pageHeight;
                }
                doc.save( 'file.pdf');﻿
             }
          });
            setInterval(function(){ 
              $(".addLoader").removeClass("cf-loader"); 
              $(".table tr.printPdfMargin td").removeClass("print-pdf-margin-set-flags");
              $(".table tr.printPdfMarginSecond td").removeClass("print-pdf-margin-flags-extra");
              $(".table tr.printPdfMarginLongData td").removeClass("print-pdf-margin-large-flags");
            }, 3000);   
      });
    }); 
</script>
 
@endsection
