$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//  $.notify.defaults({
//     globalPosition: 'bottom right'
//   });

// $(document).ajaxComplete(function() {
//     var args, ref, ref1, xhr;
//     var objects = arguments;
    
//     xhr = objects[1];
//     if ((ref = xhr.status) === 201 || ref === 202 || ref === 203) {
//       return $.notify(xhr.responseText.message, 'success');
//     } else if ((ref1 = xhr.status) === 200) {
//       return $.notify(xhr.responseText.message, 'error');
//     }
//   });

 
$('.validateRefernceCode').change(function (event) { 
    // $(".cf-loader").removeClass('hidden');
    var controlObj = $(".reference_code");
    controlObj.closest('.form-row').find('input').after('<span class="cf-loader"></span>');
    controlObj.closest('.form-row').find('.parsley-errors-list').find('.refCodeError').remove();
    controlObj.closest('form').find('button[type="submit"]').attr('disabled','disabled');

    $.ajax({
        url: BASEURL+"/patients/"+PATIENT_ID+"/validatereferncecode",
        type: "POST",
        data: {
            reference_code: $(this).val()
        },
        dataType: "JSON",
        success: function (response) {
            if (!response.data)
            {   
                controlObj.closest('.form-row').find('.parsley-errors-list').html('<li class="parsley-required refCodeError">Reference code already taken</li>')
                controlObj.val('');               
            }
            controlObj.closest('.form-row').find('.cf-loader').remove();
            controlObj.closest('form').find('button[type="submit"]').removeAttr('disabled');

        }
    });
    
});


$('.authUserEmail').change(function (event) { 
  var controlObj = $(".authUserEmail");
    controlObj.closest('.form-row').find('input').after('<span class="cf-loader"></span>');
    controlObj.closest('.form-row').find('.parsley-errors-list').find('.emailError').remove();
    controlObj.closest('form').find('button[type="submit"]').attr('disabled','disabled');
    var USER_ID = $(this).attr('objectId');
    if($(this).attr('objectType')=='hospital')
      var URL = BASEURL+"/admin/users/"+USER_ID+"/authuseremail";
    else
      var URL = BASEURL+"/users/"+USER_ID+"/authuseremail";

    $.ajax({
        url: URL ,
        type: "POST",
        data: {
            email: $(this).val()
        },
        dataType: "JSON",
        success: function (response) {
            if (!response.data)
            {   
                controlObj.closest('.form-row').find('.parsley-errors-list').html('<li class="parsley-required emailError">Email already taken</li>');
                controlObj.val('');  
            }
            controlObj.closest('.form-row').find('.cf-loader').remove();
            controlObj.closest('form').find('button[type="submit"]').removeAttr('disabled');
            // $(".cf-loader").addClass('hidden');
        }
    });
    
 
});

$('.generate_new_password').click(function (event) { 

    if (confirm('Are you sure you want to generate new password for patient ?') === false) {
        return;
    }
    $("#generatePassword").addClass('cf-loader'); 
    var PATIENT_ID = $(this).attr('object-id');
 
    $.ajax({
        url: "/admin/patients/"+PATIENT_ID+"/resetpassword",
        type: "POST",
        
        dataType: "JSON",
        success: function (response) {
            $("#generatePassword").removeClass('cf-loader'); 
            $("#generatePassword").html(response.data); 
        }
    });
    
 
});



$("input[name='has_all_access']").on("click", function(){
       check = $(this).is(":checked");
       if(check) {
           $(".add_user_associates").addClass('hidden');
       } else {
           $(".add_user_associates").removeClass('hidden');
       }
});
 

$('select[name="updateSubmissionStatus"]').change(function (event) { 
   var status = $(this).val();
   var responseId = $(this).attr('object-id');
   $('input[name="updateSubmissionStatus"]').val(status);
   
   $('#myModal').modal({ backdrop: 'static', keyboard: false });
  

   // $("#statusLoader").removeClass('hidden');

   //ajax call

   // $(this).closest('form').submit();
  //  $.ajax({
  //   url: BASEURL+"/submissions/"+responseId+"/updatesubmissionstatus",
  //   type: "POST",
  //   data: {
  //       status: status
  //   },
  //   dataType: "JSON",
  //   success: function (response) {
  //     $("#statusLoader").addClass('hidden');
  //       // if (!response.data)
  //       // {   
  //       //     alert('Reference Code Already Taken');
  //       //     $("#reference_code").val('');
  //       // }

  //       // $(".cf-loader").addClass('hidden');
  //   }
  // });

});


var uploader = new plupload.Uploader({
    runtimes : 'html5,flash,silverlight,html4',
     
    browse_button : 'pickfiles', // you can pass in id...
    container: document.getElementById('hospital_logo'), // ... or DOM Element itself
     
    url: '/admin/hospital/'+HOSPITAL_ID+'/uploadlogo',
    
    headers: {
            "x-csrf-token": $("[name=_token]").val()
        },

    filters : {
        max_file_size : '10mb',
        mime_types: [
            {title : "Image files", extensions : "jpg,gif,png"},
            {title : "Zip files", extensions : "zip"}
        ]
    },
 
 
    init: {
        PostInit: function() {
             
                uploader.start();
                return false;
             
        },
 
        FilesAdded: function(up, files) {
            uploader.start();
            $('.loader').removeClass('hidden');
        },
 
        UploadProgress: function(up, file) {
    
            $('.loader').html('<div class="progress-bar progress-bar-black animate-progress-bar" data-percentage="' + file.percent +'%" style="width: ' + file.percent +'%;"></div>');
        },

        FileUploaded: function (up, file, xhr) {
            fileResponse = JSON.parse(xhr.response);
            $('#pickfiles').addClass('hidden');
            var imgStr = '<img src="'+ fileResponse.data.image_path +'" height="50px" class="imageUploaded">';
            // imgStr += '<a class="deleteHospitalLogo" data-type="hospital" data-value="'+HOSPITAL_ID+'" href="javascript:;">[delete]</a>';
            $('#hospital_logo').val(fileResponse.data.filename);
            $('#hospital_logo_block').append(imgStr);
            $('.loader').addClass('hidden');
            $('.deleteHospitalLogo').removeClass('hidden');

         },
 
        Error: function(up, err) {
            // document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
        }
    }
});
 
uploader.init();


$('.upload').on('click', '.deleteHospitalLogo', function(event) {

    if (confirm('Are you sure you want to delete this hospital logo?') === false) {
        return;
    }

    var hospitalId = $(this).attr("data-value");
    var imageName ='';
    if(!hospitalId)
    {
        imageName = $("#hospital_logo").val();
        $('.imageUploaded').remove();
        $('#pickfiles').removeClass('hidden');
        $('.deleteHospitalLogo').addClass('hidden');
    }
    else
    {
        $.ajax({
            url: BASEURL + "/admin/hospital/" + hospitalId + "/deletelogo",
            type: "POST",
            data: {
                imageName: imageName
            },
            success: function (response) {
               $('.imageUploaded').remove();
               $('#pickfiles').removeClass('hidden');
               $('.deleteHospitalLogo').addClass('hidden');
            }
        });
    }

    

});


$('.addAttributes').click(function (event) { 

    var attribute_name = $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="attribute_name[]"]').val();
    var control_type = $(this).closest('.attributes_block').find('.addAttributeBlock').find('select[name="controltype[]"]').val();
    var control_type_values = $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="controltypevalues[]"]').val();
    var counter = $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="counter"]').val(); 
    var newCounter = parseInt(counter)+1;
 

    var validate = $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="validate['+counter+']"]');
    var checked = (validate.is(":checked")) ?'checked' :'';  
    
    var err= 0;

    if(attribute_name=='')
    {
        alert('Please enter label');
        err++;
    }
    else if(control_type=='')
    {
        alert('Please enter control type')
        err++;
    }

    if ((control_type == 'select' || control_type == 'multiple') && control_type_values=='')
    {
        alert('Please enter values')
        err++;
    }

    if(err==0)
    {
        html ='<div class="row m-b-10 allattributes attributeContainer">';

        html +='<div class="col-xs-3">';
        html +='<input type="text" name="attribute_name[]" class="form-control" value="'+ attribute_name +'" placeholder="Enter Attribute Name"  >';
        html +='<input type="hidden" name="attribute_id[]" class="form-control" value=""></div>';

        html +='<div class="col-xs-3">';
        html +='<select name="controltype[]" class="select2-container select2 form-control">';       
        html +='<option value="">Select Control Type</option>';
        html +='<option value="textbox"> Text Box</option>';
        html +='<option value="select">Select Box</option>';
        html +='<option value="multiple"> Multiple Select Box</option>';
        html +='<option value="number"> Number </option>';
        html +='<option value="weight"> Weight </option>';
        html +='</select>';
        html +='</div>';

     
        html +='<div class="col-md-4">';
        html +='<input type="text" name="controltypevalues[]" value="'+ control_type_values +'" data-role="tagsinput" class="tags text-100">';
        html +='</div>';

        html +='<div class="col-md-1">';
        html +='<div class="validateCheck"><input type="checkbox" name="validate['+counter+']" '+checked+'></div>';
        html +='</div>';

        html +='<div class="col-md-1 text-center">';
        html +='<div class="deleteProject">';
        // html +='<a class="text-primary hidden"><i class="fa fa-close"></i></a>';
        // html +='<div class="text-right">';
        html +='<a class="text-primary deleteProjectAttributes hidden"><i class="fa fa-trash"></i></a>';
        html +='</div>';
        html +='</div>';


        $(".addAttributeBlock").before(html);
        $(".allattributes:last").find('.deleteProjectAttributes').removeClass('hidden');
        $(".allattributes:last").find('select').val(control_type);
        $(".tags").tagsinput("");

        $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="attribute_name[]"]').val('');
        $(this).closest('.attributes_block').find('.addAttributeBlock').find('select[name="controltype[]"]').val('');
        $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="controltypevalues[]"]').tagsinput('removeAll');
        validate.removeAttr('checked');
        validate.attr('name','validate['+newCounter+']');
        $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="counter"]').val(newCounter)
        
    }
});

$('.attributes_block').on('change', 'select', function(event) {
    if ($(this).val() == 'weight') {
        
        $(this).closest('.attributeContainer').find('input[name="controltypevalues[]"]').val('');
        $(this).closest('.attributeContainer').find('input[name="controltypevalues[]"]').attr('disabled','disabled');
         
    }
    else
    {
        $(this).closest('.attributeContainer').find('input[name="controltypevalues[]"]').removeAttr('disabled');
    }
});

$('.weightQuestion').change(function (event) { 

    if ($(this).hasClass("weight-kg")) {  
        $(".weight-kg").attr('readonly',false);
        $(".weight-st").val('');
        $(".weight-st").attr('readonly',true);
        $(".weight-lb").val('');
        $(".weight-lb").attr('readonly',true);
    }
    else
    {  
        $(".weight-kg").val('');
        $(".weight-kg").attr('readonly',true);
        $(".weight-st").attr('readonly',false);
        $(".weight-lb").attr('readonly',false);
    }

    if($(this).val()=='')
    {  
       if ($(this).hasClass("weight-kg")) {     
           $(".weight-st").attr('readonly',false);
           $(".weight-lb").attr('readonly',false); 
       }
       else if($(".weight-st").val()=='' && $(".weight-lb").val()=='')    
       {
            $(".weight-kg").attr('readonly',false);
       }
    }
});


$('.attributes_block').on('click', '.deleteProjectAttributes', function(event) {
    if (confirm('Are you sure you want to delete this project attribute?') === false) {
        return;
    }

     var attributeId = $(this).closest('.attributeContainer').find('input[name="attribute_id[]"]').val();
     var obj = $(this);
     if(attributeId=='')
        obj.closest('.attributeContainer').remove();
     else
     {
        $.ajax({
            url: BASEURL+"/attributes/" + attributeId,
            type: "DELETE",
            success: function (response) {
               obj.closest('.attributeContainer').remove();
            }
        });
     }

});



function authHospitalList(Obj ,hospitalObj)
{
    var result = true;

    Obj.closest('.allHospitalsAccess').find('select[name="hospital[]"]').each(function () {

        if (hospitalObj.get(0) != $(this).get(0) && $(this).val() == hospitalObj.val()) {
            alert('Hospital Already Selected');
            hospitalObj.val('');
            result = false;

        }
    });
    return result;
} 

$('.allHospitalsAccess').on('change', 'select[name="hospital[]"]', function(event) {
    authHospitalList($(this) ,$(this));
});



 
$('.add_user_associates').on('click', '.add-hospital-user', function(event) {

    var objectType = $(this).attr('object-type');

    if($(".hospital_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
        return;
    }

    if(!authHospitalList($(this) ,$(".hospital_users:last").find('select')))
    {

         return;
    }
    

    var addHospital = $(".hospital_users:last").find('select').html(); 
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;

    html ='<div class="row hospital_users add-user-container">';
    html +='<div class="col-md-4">';
    html +='<input type="hidden" name="user_access[]" value="">';
    html +='<select name="hospital[]" id="hospital" class="select2 form-control"  >';
    html += addHospital
    html +='<select>';
    html +='</div>';
               
    html +='<div class="col-md-4">';
    html +='<div class="radio radio-primary text-right">';
    html +='<input id="access_view_'+i+'" type="radio" name="access_'+i+'" value="view" checked="checked">';
    html +='<label for="access_view_'+i+'">View</label>';
    html +='<input id="access_edit_'+i+'" type="radio" name="access_'+i+'" value="edit">';
    html +='<label for="access_edit_'+i+'">Edit</label>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-md-4 text-center">';
    html +='<a class="deleteUserHospitalAccess hidden"> Delete </a><button type="button"  object-type="Hospital" class="btn btn-link text-success pullleft add-hospital-user"><i class="fa fa-plus"></i> Add Hospital</button>';
    html +='</div>';

    html +='</div>';

    $('input[name="counter"]').val(i);
    $(".hospital_users:last").removeClass('add-user-container');
    $(".hospital_users:last").find('.add-hospital-user').remove();
    $(".hospital_users:last").find('.deleteUserHospitalAccess').removeClass('hidden');
    $(".hospital_users:last").after(html);
    

});

$('.add-mediaction').click(function (event) { 

    if($(".patient-mediaction:last").find('input').val()=='')
    {
        alert('Please Enter Medication ');
        return;
    }

    html ='<div class="row patient-mediaction">';
    html +='<div class="col-sm-6 m-t-25 ">';
    html +='<input name="medications[]" id="medications" type="text"  class="form-control" placeholder="Enter Medication" >';
    html +='</div>';
    html += '<div class="col-sm-1 text-right m-t-25 delete-medication">';
    html +='<button type="button" class="btn btn-white delete-madication hidden"><i class="fa fa-trash"></i></button>';
    html +='</div>';
    html +='</div>';

    $(".patient-mediaction:last").find('.delete-madication').removeClass('hidden');

    $(".patient-mediaction:last").after(html);

});




$('.medication-data').on('click', '.delete-madication', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    $(this).closest('.patient-mediaction').remove();
});


$('.add-visit').click(function (event) { 

    if($(".patient-visit:last").find('input').val()=='')
    {
        alert('Please Enter Visit ');
        return;
    }


    html ='<div class="row patient-visit">';
    html +='<div class="datetime">'; 
    html +='<div class="col-sm-3 m-t-25 form-group">';
    html +='<div class="input-group date datetimepicker">';
    html +='<input name="visit_date[]" id="visit_date" type="text"   placeholder="Enter Date" class="form-control"/>';
    html +='<span class="input-group-addon" >';
    html +='<span class="glyphicon glyphicon-calendar"></span>';
    html +='</span>';
    html +='</div>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-sm-6 m-t-25 ">';
    html +='<textarea name="note[]" id="note" type="text"   placeholder="Enter Note" class="form-control"></textarea> ';
    html +='</div>';
    html += '<div class="col-sm-1 text-right m-t-25 delete-medication">';
    html +='<button type="button" class="btn btn-white delete-visit hidden"><i class="fa fa-trash"></i></button>';
    html +='</div>';
    html +='</div>';

    $(".patient-visit:last").find('.delete-visit').removeClass('hidden');

    $(".patient-visit:last").after(html);

    $(".patient-visit:last").find('.datetimepicker').datetimepicker({
        format: 'DD-MM-YYYY HH:mm'
    });

});


$('.visit-data').on('click', '.delete-visit', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    $(this).closest('.patient-visit').remove();
});


function authProjectList(Obj ,projectObj)
{
    var result = true;

    Obj.closest('.allProjectsAccess').find('select[name="projects[]"]').each(function () {

        if (projectObj.get(0) != $(this).get(0) && $(this).val() == projectObj.val()) {
            alert('Project Already Selected');
            projectObj.val('');
            result = false;

        }
    });
    return result;
} 

$('.allProjectsAccess').on('change', 'select[name="projects[]"]', function(event) {
    authProjectList($(this) ,$(this));
});

 
$('.add_user_associates').on('click', '.add-project-user', function(event) {

    var objectType = $(this).attr('object-type');

    if($(".project_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
        return;
    }

    if(!authProjectList($(this) ,$(".project_users:last").find('select')))
    {
         return;
    }

    var addProjects = $(".project_users:last").find('select').html(); 
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;

    html ='<div class="row project_users add-user-container">';
    html +='<div class="col-md-4">';
    html +='<input type="hidden" name="user_access[]" value="">';
    html +='<select name="projects[]" id="projects" class="select2 form-control"  >';
    html += addProjects
    html +='<select>';
    html +='</div>';
    html +='<div class="col-md-4">';
    html +='<div class="radio radio-primary text-right">';
    html +='<input id="access_view_'+i+'" type="radio" name="access_'+i+'" value="view" checked="checked">';
    html +='<label for="access_view_'+i+'">View</label>';
    html +='<input id="access_edit_'+i+'" type="radio" name="access_'+i+'" value="edit">';
    html +='<label for="access_edit_'+i+'">Edit</label>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-md-4 text-center">';
    html +='<a class="deleteUserProjectAccess hidden"> Delete </a><button type="button"  object-type="Project" object-id="" class="btn btn-link text-success pullleft  add-project-user"><i class="fa fa-plus"></i> Add Project</button>';
    html +='</div>';
    html +='</div>';

    $('input[name="counter"]').val(i);
    $(".project_users:last").removeClass('add-user-container');
    $(".project_users:last").find('.add-project-user').remove();
    $(".project_users:last").find('.deleteUserProjectAccess').removeClass('hidden');
    $(".project_users:last").after(html);


});

function validateHospitalUser()
{
    var duplicateHospital =  [];
    var allHospitals =  []; 
    $('select[name="hospital[]"]').each(function() {
            hospital = $(this).val();
            allHospitals.push(hospital);
        
    });

    var sorted_arr = allHospitals.sort();  
    for (var i = 0; i < allHospitals.length-1; i++) {
        if (sorted_arr[i + 1] == sorted_arr[i]) {
            duplicateHospital.push(sorted_arr[i]);
        }
    } 
   
    if (duplicateHospital!='') {
        alert('Duplicate hospital entry made');
        return false;
    }
    
}

$('.add_user_associates').on('click', '.deleteUserHospitalAccess', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    var userAccessId = $(this).attr("data-id");

    if(userAccessId)
    {
        $.ajax({
            url: BASEURL + "/admin/user-access/" + userAccessId,
            type: "DELETE",
            success: function (response) {
 
            }
        });
    }

    $(this).closest('.hospital_users').remove();
    
 
});

$('.add_user_associates').on('click', '.deleteUserProjectAccess', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    var userAccessId = $(this).attr("data-id");

    if(userAccessId)
    {
        $.ajax({
            url: BASEURL + "/delete-user-access/" + userAccessId,
            type: "DELETE",
            success: function (response) {
 
            }
        });
    }

    $(this).closest('.project_users').remove();
    
 
});

function lineChartWithOutBaseLine(chartData,legends,container,xaxisLable,yaxisLabel)
{
    graphs = _.map(legends, function(value, key){ 
        var graphObj = {
        "balloonText": " "+value+" on [[category]]: [[value]]",
        "bullet": "round",
        "title": value,
        "valueField": key,
        "dashLength": 2,
        "inside": true

        };

        return graphObj;
    })
   

    var chart = AmCharts.makeChart(container, {
        "type": "serial",
        "theme": "light",
        // "marginRight": 40,
        // "marginLeft": 40,
        // "autoMarginOffset": 20,
        "legend": {
            "useGraphSettings": true
        },
        "dataProvider": chartData,
        "valueAxes": [{
            "integersOnly": true,
            "maximum": 20,
            "minimum": 0,
            "reversed": false,
            "axisAlpha": 0,
            "dashLength": 5,
            "position": "top",
            "title": yaxisLabel
        }],
         "valueAxes": [{
            "logarithmic": false,
            "dashLength": 0,
            "position": "left",
            "title": yaxisLabel
         }],

        "graphs": graphs,
        "chartCursor": {
            "cursorAlpha": 0,
            "zoomable": false
        },
        // "chartScrollbar": {
          
        //  },
        "categoryField": "Date",
        "categoryAxis": {
            "gridPosition": "start",
            "markPeriodChange": false,
            "minHorizontalGap": 100,
            // "labelRotation": 45,
            "axisAlpha": 0,
             "fillColor": "#000000",
            "gridAlpha": 0,
              "position": "bottom",
            "title": xaxisLable
        },
        "export": {
          "enabled": true,
            "position": "bottom-right"
         }
    });

    amchartsNoData(chart);
  //   chart.addListener("dataUpdated", zoomChart);
  //   function zoomChart() {
  //   if (chart.zoomToIndexes) {
  //       chart.zoomToIndexes(130, chartData.length - 1);
  //   }
  // }
}

function lineChartWithBaseLine(chartData,legends,baselineScore,container,xaxisLable,yaxisLabel)
{
    graphs = _.map(legends, function(value, key){ 
        var graphObj = {
          "balloonText": value+" on [[category]]: [[value]]",
              "bullet": "round",
              "title": value,
              "valueField": key,
             "dashLength": 2,
             "inside": true
        };

        return graphObj;
    })

    var baseLineObj = {
        "balloonText": "Baseline in [[category]]: [[value]]",
        "type":"step",
        "lineThickness": 1,
        "title": "Baseline",
        "valueField": "baseLine",
        "bullet": "square",
    
    }
   graphs.push(baseLineObj); 

     var chart = AmCharts.makeChart(container, {
          "type": "serial",
          "theme": "light",
          // "marginRight": 40,
          // "marginLeft": 40,
          // "autoMarginOffset": 20,
          "legend": {
              "useGraphSettings": true
          },
          "dataProvider": chartData,
          "valueAxes": [{
              "integersOnly": true,
              "maximum": 50,
              "minimum": 0,
              "axisAlpha": 0,
              "dashLength": 5,
              "position": "left",
              "title": yaxisLabel
          }],
           "valueAxes": [{
              "logarithmic": false,
              "dashLength": 0,
              // "guides": [{
              //     "dashLength": 6,
              //     "inside": true,
              //     "label": "Baseline",
              //     "lineAlpha": 1,
              //     "value": baselineScore,
         
              // }],
              "position": "left",
              "title": yaxisLabel
               }],
         
          "graphs": graphs,
          "chartCursor": {
              "cursorAlpha": 0,
              "zoomable": false
          },
         //  "chartScrollbar": {
          
         // },
          "categoryField": "Date",
          "categoryAxis": {
              "gridPosition": "start",
              "markPeriodChange": false,
              "minHorizontalGap": 100,
              // "labelRotation": 45,
              "axisAlpha": 0,
               "fillColor": "#000000",
              "gridAlpha": 0,
                "position": "bottom",
              "title": xaxisLable
          },
          "export": {
            "enabled": true,
              "position": "bottom-right"
           }
         });

     amchartsNoData(chart);
     // chart.addListener("dataUpdated", zoomChart);
     // function zoomChart() {
     //    if (chart.zoomToIndexes) {
     //      console.log(chartData.length);
     //        chart.zoomToIndexes(130, chartData.length - 1);
     //    }
     //  }
}

function shadedLineChartWithBaseLine(chartData,label,baseLine,container,xaxisLable,yaxisLabel)
{  
    var chart = AmCharts.makeChart(container, {
         "type": "serial",
         "theme": "light",
         // "marginRight": 40,
         //  "marginLeft": 40,
         //  "autoMarginOffset": 20,
         "legend": {
             "useGraphSettings": true
         },
         "dataProvider": chartData,
         "valueAxes": [{
             "integersOnly": true,
             "maximum": 6,
             "minimum": 1,
             "reversed": true,
             "axisAlpha": 0,
             "dashLength": 5,
             "position": "left",
             "title": yaxisLabel
         }],
         
          "valueAxes": [{
             "logarithmic": false,
             "dashLength": 0,
             "position": "left",
            "title": yaxisLabel
              }],
         
         "graphs": [ {
             "balloonText": "[[category]]: [[value]]",
             "bullet": "round",
             "title": "Score",
             "lineColor": "#05A8A5",
             "valueField": "score",
             "fillColor": "#ecb42f",
             "fillAlphas": 0.2,
              "dashLength": 2,
              "hidden":false,
               "inside": true
         
         },
         {
             "balloonText": "[[category]]: [[value]]",
             "type":"step",
             "bullet": "square",
             "title": "Baseline",
             "lineColor": "#333",
             "valueField": "baseLine",
              "dashLength": 2,
              "hidden":false,
               "inside": true
         
         }
         ],
         
         "chartCursor": {
             "cursorAlpha": 0,
             "zoomable": false
         },
         //  "chartScrollbar": {
          
         // },
         "categoryField": "Date",
         "categoryAxis": {
             "gridPosition": "start",
             "markPeriodChange": false,
              "minHorizontalGap": 100,
             "axisAlpha": 0,
              "fillColor": "#000000",
             "gridAlpha": 0,
               "position": "bottom",
             "title": xaxisLable
         },
         "export": {
           "enabled": true,
             "position": "bottom-right"
          }
         });

    amchartsNoData(chart);
  //   chart.addListener("dataUpdated", zoomChart);
  //   function zoomChart() {
  //   if (chart.zoomToIndexes) {
  //       chart.zoomToIndexes(20, chartData.length - 1);
  //   }
  // }
}

function patientFlagsChart(chartData)
{
         var chart = AmCharts.makeChart("chartdiv", {
        "type": "serial",
        "theme": "light",
        "legend": {
            "useGraphSettings": true
        },
        "dataProvider": chartData,
        "valueAxes": [{
            "integersOnly": true,
            "maximum": 6,
            "minimum": 1,
            "reversed": false,
            "axisAlpha": 0,
            "dashLength": 5,
            "position": "left",
            "title": "Total Score"
        }],
        
        "graphs": [{
            "balloonText": "Red Flag on [[category]]: [[value]]",
            "bullet": "round",
            "title": "Red",
           "lineColor": "#CC0000",
            "valueField": "Red",
            "fillColor": "#CC0000",
            "fillAlphas": 0.2,
        "dashLength": 2,
        "inside": true
        
        }, {
            "balloonText": " Amber Flag on [[category]]: [[value]]",
            "bullet": "round",
            "title": "Amber",
            "lineColor": "#ecb42f",
            "valueField": "Amber",
           "dashLength": 2,
           "fillColor": "#ecb42f",
            "fillAlphas": 0.2,
           "hidden":false,
           "inside": true
        }, {
            "balloonText": "Green Flag on [[category]]: [[value]]",
            "bullet": "round",
            "title": "Green",
            "lineColor": "#05A8A5",
            "valueField": "Green",
            "fillColor": "#ecb42f",
            "fillAlphas": 0.2,
             "dashLength": 2,
             "hidden":false,
              "inside": true

        }],
        "chartCursor": {
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "Date",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
             "fillColor": "#000000",
            "gridAlpha": 0,
              "position": "bottom",
            "title": "Submission"
        },
        "export": {
          "enabled": true,
            "position": "bottom-right"
         }
    });

    amchartsNoData(chart);
}

function submissionChart(chartData,baseLine)
{
    var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "marginRight": 70,
    "autoMarginOffset": 20,
    "dataProvider": chartData,
    "balloon": {
        "cornerRadius": 6
    },
    // "valueAxes": [{
    //     "axisAlpha": 0
    // }],
    "valueAxes": [{
    "logarithmic": true,
    "dashLength": 1,
    "guides": [{
        "dashLength": 6,
        "inside": true,
        "label": "Baseline",
        "lineAlpha": 1,
        "value": baseLine
    }],
       }],
    "graphs": [{
        "balloonText": "[[category]]<br><b><span style='font-size:14px;'>[[value]] Score</span></b>",
        "bullet": "round",
        "bulletSize": 6,
        "connect": false,
        "lineColor": "#b6d278",
        "lineThickness": 2,
        "negativeLineColor": "#487dac",
        "valueField": "value"
    }],
    "chartCursor": {
        "categoryBalloonDateFormat": "DD-MM-YYYY",
        "cursorAlpha": 0.1,
        "cursorColor": "#000000",
        "fullWidth": true,
        "graphBulletSize": 2
    },
    "chartScrollbar": {},
    "dataDateFormat": "DD-MM-YYYY",
    "categoryField": "date",
    "categoryAxis": {
        "minPeriod": "DD",
        "parseDates": true,
        "minorGridEnabled": true
    }
});
    amchartsNoData(chart);

}

function submissionBarChart(chartData,container)
{
    var chart = AmCharts.makeChart(container, {
    "theme": "light",
    "type": "serial",
     "legend": {
    "useGraphSettings": true
  },
    "dataProvider": chartData,
    "valueAxes": [{
        "title": "Score",
        "maximum": 6,
        "minimum": 0,
    }],

    "graphs": [{
        "balloonText": "Previous [[category]] (: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Previous",
        "type": "column",
        "valueField": "prev"
    }, {
        "balloonText": "Baseline [[category]] : <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Baseline",
        "type": "column",
        "valueField": "base"
    },{
        "balloonText": "Current [[category]]: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Current",
        "type": "column",
        "clustered":false,
        "columnWidth":0.5,
        "valueField": "current"
    }],
    "plotAreaFillAlphas": 0.1,
    "categoryField": "question",
    "categoryAxis": {
        "gridPosition": "start"
    },
    "export": {
      "enabled": true
     }

});
    amchartsNoData(chart);

}


function miniGraph(chartData,container)
{  
    AmCharts.makeChart(container, {
    "type": "serial",
    "dataProvider": chartData,
    "valueAxes": [{
        "axisAlpha": 0,
        "gridAlpha": 0
    }],
    "graphs": [{
        "dashLength": 2,
        "inside": true,
         "title": "score",
         "lineColor": "#05A8A5",
        "valueField": "score"
    },
     {
        "type":"step",
        "lineThickness": 0.5,
        "title": "Baseline",
        "lineColor": "#576475",
        "valueField": "baseLine"
    }         ],
    "marginTop": 0,
    "marginRight": 0,
    "marginLeft": 0,
    "marginBottom": 0,
    "autoMargins": false,
    "categoryField": "day",
    "categoryAxis": {
        "axisAlpha": 0,
        "gridAlpha": 0
    }
});
     
}

function amchartsNoData(chart)
{
    AmCharts.checkEmptyData = function (chart) {
        if ( 0 == chart.dataProvider.length ) {
            // set min/max on the value axis
            chart.valueAxes[0].minimum = 0;
            chart.valueAxes[0].maximum = 100;
            
            // add dummy data point
            var dataPoint = {
                dummyValue: 0
            };
            dataPoint[chart.categoryField] = '';
            chart.dataProvider = [dataPoint];
            
            // add label
            chart.addLabel(0, '50%', 'The chart contains no data', 'center');
            
            // set opacity of the chart div
            chart.chartDiv.style.opacity = 0.5;
            
            // redraw it
            chart.validateNow();
        }
    }

    AmCharts.checkEmptyData(chart);
}


function drawPieChart(container,chartData,startDuration)
{
    var chart = AmCharts.makeChart( container, {
        "type": "pie",
        "theme": "light",
        "dataProvider": chartData,
        "titleField": "title",
        "valueField": "value",
        "labelRadius": 5,
        "startDuration" : startDuration,
        "radius": "36%",
        "innerRadius": "60%",
        "labelText": "[[title]]",
        "export": {
            "enabled": true
        }
     } );
    
    pieChartNoData(chart); 
}

function pieChartNoData(chart)
{
    AmCharts.addInitHandler(function(chart) {
  
      // check if data is mepty
      if (chart.dataProvider === undefined || chart.dataProvider.length === 0) {
        // add some bogus data
        var dp = {};
        dp[chart.titleField] = "";
        dp[chart.valueField] = 1;
        chart.dataProvider.push(dp)
        
        var dp = {};
        dp[chart.titleField] = "";
        dp[chart.valueField] = 1;
        chart.dataProvider.push(dp)
        
        var dp = {};
        dp[chart.titleField] = "";
        dp[chart.valueField] = 1;
        chart.dataProvider.push(dp)
        
        // disable slice labels
        chart.labelsEnabled = false;
        
        // add label to let users know the chart is empty
        chart.addLabel("50%", "50%", "The chart contains no data", "middle", 15);
        
        // dim the whole chart
        chart.alpha = 0.3;
      }
      
    }, ["pie"]);
}



// function miniGraph(chartData,container)
// {
//     // line chart, with different line color below zero         
//     var chart = new AmCharts.AmSerialChart(AmCharts.themes.none);
//     chart.dataProvider = chartData;
//     chart.categoryField = "submission";
//     chart.autoMargins = false;
//     chart.marginLeft = 0;
//     chart.marginRight = 5;
//     chart.marginTop = 0;
//     chart.marginBottom = 0;

//     var graph = new AmCharts.AmGraph();
//     graph.valueField = "score";
//     graph.showBalloon = false;
//     graph.lineColor = "#ffbf63";
//     graph.negativeLineColor = "#289eaf";
//     chart.addGraph(graph);

//     var baseLine = new AmCharts.AmGraph();
//     baseLine.valueField = "baseLine";
//     baseLine.showBalloon = false;
//     baseLine.lineColor = "#000";
//     baseLine.negativeLineColor = "#84B761";
//     chart.addGraph(baseLine);

//     var valueAxis = new AmCharts.ValueAxis();
//     valueAxis.gridAlpha = 0;
//     valueAxis.axisAlpha = 0;
//     chart.addValueAxis(valueAxis);

//     var categoryAxis = chart.categoryAxis;
//     categoryAxis.gridAlpha = 0;
//     categoryAxis.axisAlpha = 0;
//     categoryAxis.startOnAxis = true;

//     // using guide to show 0 grid
//     // var guide = new AmCharts.Guide();
//     // guide.value = 0;
//     // guide.lineAlpha = 0.1;
//     // valueAxis.addGuide(guide);
//     chart.write(container);
 
// }


$('.sortSubmission').click(function (event) { 
       var startDate = $('input[name="startDate"]').val();
       var endDate = $('input[name="endDate"]').val();

       var sortObject = $(this);
       var sort_type = sortObject.attr('sort-type'); 
       var sort = sortObject.attr('sort'); 
       var limit = $("#submissionData").attr('limit');
       var object_type = $("#submissionData").attr('object-type');
       var object_id = $("#submissionData").attr('object-id');

       var cond_type = sortObject.closest('table').attr('cond-type'); 
       var cond = sortObject.closest('table').attr('cond'); 

       sortObject.closest('.grid-body').find(".loader-outer").removeClass('hidden');
       $.ajax({
        url: BASEURL+"/getsubmissionlist",
        type: "GET",
        data: {
            sort: sort+'-'+sort_type,
            cond: cond+'-'+cond_type,
            startDate:startDate ,
            endDate:endDate ,
            limit:limit ,
            object_type:object_type,
            object_id:object_id  
        },
        dataType: "JSON",
        success: function (response) {
            
            console.log(sort_type);  
           if(sort_type=='asc')
            { 
              sortObject.attr('sort-type','desc');
              
              sortObject.find('.sortCol').removeClass('fa-angle-down');
              sortObject.find('.sortCol').addClass('fa-angle-up');
            }
            else
            {  
              sortObject.attr('sort-type','asc');
              sortObject.find('.sortCol').addClass('fa-angle-down');
              sortObject.find('.sortCol').removeClass('fa-angle-up');

            } 
            $("#submissionData").html(response.data);
             

           sortObject.closest('.grid-body').find(".loader-outer").addClass('hidden');
           $('[data-toggle="tooltip"]').tooltip()
        }
      });

    });

 
$( document ).on("click", ".sortPatientSummary", function() {
       var startDate = $('input[name="startDate"]').val();
       var endDate = $('input[name="endDate"]').val();

       var sortObject = $(this);
       var sort_type = sortObject.attr('sort-type'); 
       var sort = sortObject.attr('sort'); 
       var limit = $("#patientSummaryData").attr('limit');
       sortObject.closest('.grid-body').find(".loader-outer").removeClass('hidden');
       $.ajax({
        url: BASEURL+"/getpatientsummarylist",
        type: "GET",
        data: {
            sort: sort+'-'+sort_type,
            startDate:startDate ,
            endDate:endDate ,
            limit:limit 
        },
        dataType: "JSON",
        success: function (response) {
 
           if(sort_type=='asc')
            { 
              
              sortObject.attr('sort-type','desc');
              
              sortObject.find('.sortCol').removeClass('fa-angle-down');
              sortObject.find('.sortCol').addClass('fa-angle-up');
            }
            else
            {  
              
              sortObject.attr('sort-type','asc');
              sortObject.find('.sortCol').addClass('fa-angle-down');
              sortObject.find('.sortCol').removeClass('fa-angle-up');

            } 

            $("#patientSummaryData").html(response.data);
            miniChartData = response.miniChartData;
              
            $.each(response.patientIds, function (index, value) {  
              chartData = $.parseJSON( miniChartData[index] );
 
              miniGraph(chartData,'chart_mini_'+value);

           }); 

          sortObject.closest('.grid-body').find(".loader-outer").addClass('hidden');
        }
      });

    });


$('.addSettings').click(function (event) { 

    var flag_count = $(this).closest('.settings_block').find('.addSettingsContainer').find('input[name="flag_count[]"]').val();
    var operation = $(this).closest('.settings_block').find('.addSettingsContainer').find('select[name="operation[]"]').val();
    var flag_colour = $(this).closest('.settings_block').find('.addSettingsContainer').find('select[name="flag_colour[]"]').val();
    var compared_to = $(this).closest('.settings_block').find('.addSettingsContainer').find('select[name="compared_to[]"]').val();
    var counter = $(this).closest('.settings_block').find('.addSettingsContainer').find('input[name="counter"]').val(); 
    var newCounter = parseInt(counter)+1;
 
   
    var err= 0;

    if(flag_count=='')
    {
        alert('Please enter Flag Count');
        err++;
    }
    else if(operation=='')
    {
        alert('Please enter operation')
        err++;
    }
    else if(flag_colour=='')
    {
        alert('Please enter Flag Colour')
        err++;
    }
    else if(compared_to=='')
    {
        alert('Please enter compared To')
        err++;
    }

     

    if(err==0)
    {
        html ='<div class="row allsettings settingsContainer">';

        html +='<div class="col-xs-2">';
        html +='<input type="text" name="flag_count[]" class="form-control" value="'+flag_count+'"  placeholder="Enter Flag Count"  >';
        html +='<input type="hidden" name="setting_id[]" class="form-control" >';
        html +='</div>';
        html +='<div class="col-xs-3">';
        html +='<select name="operation[]" class="select2-container select2 form-control">';
        html +='<option value="">Select Operation</option>';
        html +='<option value="greater_than" >Greater Than</option>';
        html +='<option value="greater_than_equal_to">Greater Than Equal To</option>';
        html +='<option value="less_than">Less Than</option>';
        html +='<option value="less_than_equal_to" > Less Than Equal To </option>';
        html +='</select>';
                     
        html +='</div>';
        html +='<div class="col-xs-3">';
        html +='<select name="flag_colour[]" class="select2-container select2 form-control">';
        html +='<option value="">Select Flag Colour</option>';
        html +='<option value="red" >Red</option>';
        html +='<option value="amber" >Amber</option>';
        html +='<option value="green" >Green</option>';
        html +='</select>';
        html +='</div>';
        html +='<div class="col-xs-3">';
        html +='<select name="compared_to[]" class="select2-container select2 form-control">';
        html +='<option value="">Select Compared To</option>';
        html +='<option value="previous"  >Previous</option>';
        html +='<option value="baseline">Baseline</option>';
        html +='</select>';
                     
        html +='</div>';
        html +='<div class="col-md-1 text-center">';
        html +='<div class="deleteSettings">';
        html +='<a class="text-primary deleteAlertSettings"><i class="fa fa-trash"></i></a>';
        html +='</div>';
        html +='</div>';
        html +='</div>';


        $(".addSettingsBlock").before(html);
        $(".allsettings:last").find('.deleteAlertSettings').removeClass('hidden');
        $(".allsettings:last").find('select[name="operation[]"]').val(operation);
        $(".allsettings:last").find('select[name="flag_colour[]"]').val(flag_colour);
        $(".allsettings:last").find('select[name="compared_to[]"]').val(compared_to);

        $(this).closest('.settings_block').find('.addSettingsContainer').find('input[name="flag_count[]"]').val('');
        $(this).closest('.settings_block').find('.addSettingsContainer').find('select[name="operation[]"]').val('');
        $(this).closest('.settings_block').find('.addSettingsContainer').find('select[name="flag_colour[]"]').val('');
        $(this).closest('.settings_block').find('.addSettingsContainer').find('select[name="compared_to[]"]').val('');

        $(this).closest('.attributes_block').find('.addAttributeBlock').find('input[name="counter"]').val(newCounter)
        
    }
});

$('.settings_block').on('click', '.deleteAlertSettings', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    var settingsId = $(this).closest(".settingsContainer").find('input[name="setting_id[]"]').val();

    if(settingsId!='')
    {
        $.ajax({
            url: BASEURL + "/delete-alert-setting/" + settingsId,
            type: "DELETE",
            success: function (response) {
                 
            }
        });
    }
     
    
    $(this).closest('.settingsContainer').remove();
 
});

$('.settings_block').on('click', '.deleteAlertSettings', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    var settingsId = $(this).closest(".settingsContainer").find('input[name="setting_id[]"]').val();

    if(settingsId!='')
    {
        $.ajax({
            url: BASEURL + "/delete-alert-setting/" + settingsId,
            type: "DELETE",
            success: function (response) {
                 
            }
        });
    }
     
    
    $(this).closest('.settingsContainer').remove();
 
});

// $('.question-list').on('change', '.questionType', function(event) { 

//     var counter = $(this).closest(".question").attr("row-count");
//     var i = parseInt(counter); 

//     $(this).closest('.question').find('.question-options-block').remove(); 
//     var html = '';

//     if($(this).val()=="input" && $('option:selected', this).attr("data-value")=="weight")
//     {
        
//         html +='<div class="col-sm-1"></div>';
//         html +='<div class="col-sm-10 question-options-block m-t-15 hidden">';
//         html +='<div class="row"><input type="hidden" name="optionId['+i+'][0]" value="">';
//         html +='<div class="col-sm-6 m-t-10 m-b-10 ">';
//         html +='<input name="option['+i+'][0]" id="question" type="hidden" placeholder="Enter option" value="kg" class="form-control" >';
//         html +='</div>';
//         html +='<div class="col-sm-4 m-t-10 m-b-10 ">';
//         html +='<input name="score['+i+'][0]" id="question" type="hidden" placeholder="Enter score" value="1" class="form-control" >';
//         html +='</div> ';
//         html +='<div class="col-sm-2 text-center m-t-10 m-b-10">';
//         html +='</div>';
//         html +='</div>';
//         html +='<div class="col-sm-1"></div>';


//         html +='<div class="row"><input type="hidden" name="optionId['+i+'][1]" value="">';
//         html +='<div class="col-sm-6 m-t-10 m-b-10 ">';
//         html +='<input name="option['+i+'][1]" id="question" type="hidden" placeholder="Enter option" value="st" class="form-control" >';
//         html +='</div>';
//         html +='<div class="col-sm-4 m-t-10 m-b-10 ">';
//         html +='<input name="score['+i+'][1]" id="question" type="hidden" placeholder="Enter score" value="2" class="form-control" >';
//         html +='</div> ';
//         html +='<div class="col-sm-2 text-center m-t-10 m-b-10">';
//         html +='</div>';
//         html +='</div>';


//         html +='<div class="row"><input type="hidden" name="optionId['+i+'][2]" value="">';
//         html +='<div class="col-sm-6 m-t-10 m-b-10 ">';
//         html +='<input name="option['+i+'][2]" id="question" type="hidden" placeholder="Enter option" value="lb" class="form-control" >';
//         html +='</div>';
//         html +='<div class="col-sm-4 m-t-10 m-b-10 ">';
//         html +='<input name="score['+i+'][2]" id="question" type="hidden" placeholder="Enter score" value="3" class="form-control" >';
//         html +='</div> ';
//         html +='<div class="col-sm-2 text-center m-t-10 m-b-10">';
//         html +='</div>';
//         html +='</div>';


//         html +='</div>';
//     }
//     else if($(this).val()=="single-choice" || $(this).val()=="multi-choice" || $(this).val()=="input")
//     {

//         $(this).closest('.questionHead').find('.accordion-toggle').removeClass('hidden');
//         html +='<div class="row panel-collapse collapse in p-l-15 p-r-15" id="collapse-'+i+'">';
//         // html +='<div class="col-sm-1"></div>';
//         html +='<div class="col-sm-12 question-options-block">';
//         if(!$(this).hasClass('subquestionType'))
//         {
//             html +='<div class="row gray-section">';
//             html +='<div class="col-md-12">';
//             html +='<strong>Enter the options for this question</strong>';
//             html +='<p>You can add a sub question too. The score declares the severity of the patient</p>';
//             html +='</div>';
//             html +='</div>';
//         }
              
        
//         html +='<div class="option-block">';
//         html +='<div class="row"><input type="hidden" name="optionId['+i+'][0]" value="">';
//         html +='<div class="col-md-1"><label class="p-t-15">option 1</label></div>';
//         html +='<div class="col-md-11">';
//         html +='<div class="row">';
//         html +='<div class="col-sm-5 m-t-10 m-b-10  ">';
//         html +='<input name="option['+i+'][0]" id="question" type="text" placeholder="Enter option" class="form-control" data-parsley-required>';
//         html +='</div>';
//         html +='<div class="col-sm-2 m-t-10 m-b-10 ">';
//         html +='<input name="score['+i+'][0]" id="question" type="number" min="0" placeholder="Enter score" class="form-control" data-parsley-required>';
//         html +='</div> ';

//         if($(this).val()=="single-choice" && !$(this).hasClass('subquestionType'))
//         {
//             html +='<div class="col-sm-3 text-center m-t-20">';
//             html +='<input type="checkbox" class="js-switch hasSubQuestion" name="hasSubQuestion['+i+'][0]"><small class="help-text">HAS SUB-QUESTION</small>';
//             html +='</div>';
//             html +='<div class="col-sm-2 text-right m-t-10 m-b-10 ">';
//             html +='<button type="button" class="btn btn-white add-option" counter-key="0">Another Option <i class="fa fa-plus"></i></button>';
//             html +='</div>';
//         }
//         else
//         {
//             html +='<div class="col-sm-5 text-right m-t-10 m-b-10 ">';
//             html +='<button type="button" class="btn btn-white add-option" counter-key="0">Another Option <i class="fa fa-plus"></i></button>';
//             html +='</div>';
//         }
//         html +='</div>';
//         html +='</div>';

//         html +='</div>';
//         html +='<div class="subQuestion-container"></div> ';
//         html +='</div> ';
//         html +='</div> ';
//         html +='<div class="col-sm-1"></div>';
//         html +='</div> ';


       
//     }

//     if(($(this).val()=="input" && $('option:selected', this).attr("data-value")=="weight") || $(this).val()=="descriptive")
//     {
//         $(this).closest('.questionHead').find('.accordion-toggle').addClass('hidden');
//     }
    
//     $(this).closest('.question').find('.questionHead').after(html);
    
//     if($(this).val()=="single-choice" && !$(this).hasClass('subquestionType'))
//     {

//         $('.js-switch').each(function() {  
//             if(_.isUndefined($(this).attr('data-switchery')))
//             {
//                 var switchery = new Switchery(this, { color: '#0aa699', size: 'small' });
//             }
             
//         });
//     }
        
// });

// $('.question-list').on('change', '.hasSubQuestion', function(event) { 

//     var optionKey1 = $(this).prop('name').match(/\[(.*?)\]\[(.*?)\]/)[1];
//     var optionKey2 = $(this).prop('name').match(/\[(.*?)\]\[(.*?)\]/)[2]; 

//     if($(this).prop("checked") == true){
//         var counter = $('input[name="counter"]').val();
//         var i = parseInt(counter) + 1;

//         html ='<div class="row question subQuestion-row" row-count="'+i+'"><input type="hidden" name="questionId['+i+']" value="">';
//         html +='<div class="col-md-1"></div>';
//         html +='<div class="col-md-10 questionHead sub-question arrow_box-top gray-rbor-section">';
//         html +='<div class="col-sm-3"> <input type="hidden" name="optionKeys['+optionKey1+']['+optionKey2+']" value="'+i+'">';
//         html +='<label>Type of question</label><select name="subquestionType['+i+']" class="select2-container select2 form-control  subquestionType questionType" data-parsley-required>';
//         html +='<option value="">Select Question Type</option>';
//         html +='<option value="single-choice"> Single-choice</option>';
//         html +='<option value="multi-choice">Multi-choice</option>';
//         html +='<option value="input"> Input</option>';
//         html +='<option value="descriptive"> Descriptive </option>';
//         html +='<option value="input" data-value="weight"> Weight </option>';
//         html +='</select>';
//         html +='</div>';
//         html +='<div class="col-sm-3"><label for="">A short question identifier</label>';
//         html +='<input name="subquestionTitle['+i+']" id="subquestionTitle" type="text"   placeholder="Enter Title" class="form-control" data-parsley-required>';
//         html +='</div> ';
//         html +='<div class="col-sm-5 m-t-25">';
//         html +='<input name="subquestion['+i+']" id="subquestion" type="text"   placeholder="Enter Question" class="form-control" data-parsley-required>';
//         html +='</div> ';

//         html +='<div class="col-sm-1 text-center m-t-25 del-question-blk">';
//         html +='<button type="button" class="btn btn-white delete-question"><i class="fa fa-trash"></i></button>';
//         html +='</div>';
//         html +='</div>';
//         html +='</div>';

//         $('input[name="counter"]').val(i);
//     }
//     else
//     {
//         html ='';
//     }

//     $(this).closest('.option-block').find('.subQuestion-container').html(html);
    
    

// });

// $('.question-list').on('click', '.add-option', function(event) { 
    
//     var counter = $(this).closest(".question").attr("row-count");
//     var i = parseInt(counter);
    

//     var counterKey = $(this).attr("counter-key");
//     var j = parseInt(counterKey) + 1;

//     var question = $(this).closest(".row").find("input[name='option["+i+"]["+counterKey+"]']").val();
//     var score = $(this).closest(".row").find("input[name='score["+i+"]["+counterKey+"]']").val();

//     //add validation 
//     $(this).closest(".row").find("input[name='option["+i+"]["+counterKey+"]']").attr('data-parsley-required', 'true');
//     $(this).closest(".row").find("input[name='score["+i+"]["+counterKey+"]']").attr('data-parsley-required', 'true');


//     // if(question=='')
//     // {
//     //     alert("Please Enter Option");
//     // }
//     // else if(score=='')
//     // {
//     //     alert("Please Enter Score");
//     // }
//     // else
//     // {
//         $(this).removeClass("add-option").addClass("delete-option");
//         //$(this).find('i').removeClass("fa-plus").addClass("fa-trash");
//         $(this).html('<i class="fa fa-trash"></i></button>');  
//         // var optionBlockCount = $(this).closest(".question-options-block").find('.option-block').length; alert(optionBlockCount);
//         // $(this).closest(".question-options-block").find('.option-block:eq(-2)').find(".delete-option").removeClass("hidden");

//         html ='<div class="option-block">';
//         html +='<div class="row p-l-15 p-r-15"> <input type="hidden" name="optionId['+i+']['+j+']" value="">';
//         html +='<div class="col-sm-2"><label class="p-t-15">option '+ (j+1) +'</label></div>';
//         // html +='<div class="col-sm-10">';
//         // html +='<div class="row">';
//         html +='<div class="col-sm-5 m-t-10 m-b-10">';
//         html +='<input name="option['+i+']['+j+']" id="question" type="text" placeholder="Enter option" class="form-control" >';
//         html +='</div>';
//         html +='<div class="col-sm-2 m-t-10 m-b-10  ">';
//         html +='<input name="score['+i+']['+j+']" id="question" type="number" placeholder="Enter score" class="form-control" min="0" >';
//         html +='</div> ';

//         if($(this).closest(".question").find(".hasSubQuestion").length)
//         {
//             html +='<div class="col-sm-3 text-center m-t-20">';
//             html +='<input type="checkbox" class="js-switch hasSubQuestion" name="hasSubQuestion['+i+']['+j+']"><small class="help-text">Add sub question</small>';
//             html +='</div>';
//             html +='<div class="col-sm-2 text-right m-t-10 m-b-10 width-23">';
//             html +='<button type="button" class="btn btn-white add-option" counter-key="'+j+'">Another Option <i class="fa fa-plus"></i></button>';
//             html +='</div>';
//         }
//         else
//         {
//             html +='<div class="col-sm-5 text-right m-t-10 m-b-10 width-23">';
//             html +='<button type="button" class="btn btn-white add-option" counter-key="'+j+'">Another Option <i class="fa fa-plus"></i></button>';
//             html +='</div>';
//         }
//         // html +='</div>';
//         // html +='</div>';

//         html +='</div> ';
//         html +='<div class="subQuestion-container"></div> ';
//         html +='</div> ';


//         $(this).closest('.question-options-block').append(html);

//         if($(this).closest(".question").find(".hasSubQuestion").length)
//         {

//             $('.js-switch').each(function() {  
//                 if(_.isUndefined($(this).attr('data-switchery')))
//                 {
//                     var switchery = new Switchery(this, { color: '#0aa699', size: 'small' });
//                 }
                 
//             });
//         }
//     // }

// });

/*$('.question-list').on('click', '.delete-option', function(event) { 
    
    

    var Obj = $(this);
    var i = $(this).closest('.question').attr("row-count"); 
    var counterKey = $(this).attr("counter-key");
    var optionId = Obj.closest(".row").find('input[name="optionId['+i+']['+counterKey+']"]').val();
    
    
    var count= 0; 
    var title = '';
    var i= 0;
    if(Obj.closest('.question').hasClass("parentQuestion"))
    {  
        count=getOptionsCount(Obj);
        i = Obj.closest('.parentQuestion').attr("row-count");
        title = Obj.closest('.parentQuestion').find('input[name="title['+i+']"]').val();
    }
    else
    {  
        count=Obj.closest('.subQuestion-row').find('.option-block').length;
        i = Obj.closest('.subQuestion-row').attr("row-count"); 
        title = Obj.closest('.subQuestion-row').find('input[name="subquestionTitle['+i+']"]').val();
    }


    if(count > 2)
    {
        if (confirm('Are you sure you want to delete this option?') === false) {
            return;
        }

        Obj.closest('div').append('<span class="cf-loader"></span>');
        if(optionId!='')
        {
            $.ajax({
                url: BASEURL + "/delete-option/" + optionId,
                type: "DELETE",
                success: function (response, status, xhr) { 
                 
                    if(xhr.status==203)
                        Obj.closest('.option-block').remove(); 
                
                }
            });
        }
        else
        {
            Obj.closest('.option-block').remove(); 
        }
    }
    else
    {
        alert("Please make sure at least one option is present for question "+title+".");
    }


});
*/

function getOptionsCount(Obj)
{
    var count = 0;
    Obj.closest('.main-question_container').find('.options-list_container').each(function () { 

        if($(this).closest('.question').hasClass("main-question_container"))
        {   
            count ++;
        } 
    });

    return count;
}

// $('.add-question').click(function (event) { 
    
//     var counter = $('input[name="counter"]').val();
//     var i = parseInt(counter) + 1;
//     var j = $(".question:last").attr("row-count");
//     var questionType = $(".question:last").find("select[name='questionType["+j+"]']").val();
//     var question = $(".question:last").find("input[name='question["+j+"]']").val();
//     var title = $(".question:last").find("input[name='title["+j+"]']").val();

    

//     // if(questionType=='')
//     // {
//     //     alert("Please Enter Question Type");
//     // }
//     // else if(title=='')
//     // {
//     //     alert("Please Enter Title");
//     // }
//     // else if(question=='')
//     // {
//     //     alert("Please Enter Question");
//     // }
//     // else if(!validateInputOptions($(".question:last").find("select[name='questionType["+j+"]']")))
//     // {
//     //     alert("please enter alteast one option and score for question "+title);
         
//     // }
//     // else
//     // {
//         // $(".parentQuestion:last").find(".delete-parent-question").removeClass("hidden");

//         html ='<div class="row parentQuestion question  panel panel-default" row-count="'+i+'"><input type="hidden" name="questionId['+i+']" value="">';
//         html +='<div class="col-md-12 questionHead  panel-heading">';
//         html +='<div class="col-sm-3 m-t-15">';
//         html +='<label>Type of question</label>';
//         html +='<select name="questionType['+i+']" class="select2-container select2 form-control questionType" data-parsley-required>';
//         html +='<option value="">Select Question Type</option>';
//         html +='<option value="single-choice"> Single-choice</option>';
//         html +='<option value="multi-choice">Multi-choice</option>';
//         html +='<option value="input"> Input</option>';
//         html +='<option value="descriptive"> Descriptive </option>';
//         html +='<option value="input" data-value="weight"> Weight </option>';
//         html +='</select>';
//         html +='</div>';
//         html +='<div class="col-sm-3 m-t-15">';
//         html +='<label for="">A short question identifier</label>';
//         html +='<input name="title['+i+']" id="title" type="text"   placeholder="Enter Title" class="form-control" data-parsley-required>';
//         html +='</div> ';
//         html +='<div class="col-sm-5 m-t-15">';
//         html +='<label for="">What do you need to ask</label>';
//         html +='<input name="question['+i+']" id="question" type="text"   placeholder="Enter Question" class="form-control" data-parsley-required>';
//         html +='</div> ';

//         html +='<div class="col-sm-1 text-center m-t-15 m-b-15 del-question-blk">';
//         html +='<button type="button" class="btn btn-white delete-parent-question delete-question"><i class="fa fa-trash"></i></button>';
//         html +='</div>';
//         html +='<a class="accordion-toggle hidden" data-toggle="collapse" data-parent="#accordion" href="#collapse-'+i+'">';
//         html +='<i class="indicator glyphicon glyphicon-chevron-up pull-right chevron"></i>';
//         html +='</a>';
//         html +='</div>';
//         html +='</div>';

//         if($('.question-list').find('.no_question').length)
//             $('.question-list').find('.no_question').addClass('hidden');
            
//         $('.question-list').append(html);
//         $('input[name="counter"]').val(i);
//     // }

// });

$('.question-list').on('click', '.delete-question', function(event) { 
    
    if (confirm('Are you sure you want to delete this question?') === false) {
        return;
    }
    var Obj = $(this);

    var i = Obj.closest(".question").attr('row-count');
    var questionId = Obj.closest(".question").find('input[name="questionId['+i+']"]').val();
    Obj.closest('div').append('<span class="cf-loader"></span>');

    if(!Obj.hasClass('delete-parent-question'))
    {  
        Obj.closest('.option-block').find('.hasSubQuestion').trigger('click');
    }

    if(questionId!='')
    {
        $.ajax({
            url: BASEURL + "/delete-question/" + questionId,
            type: "DELETE",
            success: function (response) {
                 Obj.closest('.question').remove(); 

            }
        });
    }
    else
    {
        Obj.closest('.question').remove(); 
    }



    

});

$('.validateAndRedirect').click(function (event) { 

    var url = $(this).attr('url');
    
    $.confirm({
        text: "Would like to save and redirect?",
        title: "Confirmation required",
        confirm: function(button) {
            $("input[name='redirect_url']").val(url);
            $('form').submit();
        },
        cancel: function(button) {
            window.location = url;
        },
        confirmButton: "Yes",
        cancelButton: "No",
        post: true,
        // confirmButtonClass: "btn-danger",
        // cancelButtonClass: "btn-default",
    
    });

     
    

});


$('.publish-questionnaire').click(function (event) { 
    
    if (confirm('Are you sure you want to publish this questionnaire ?') === false) {
        return;
    }
     
    $("input[name='submitType']").val('publish');
    $('form').submit();
     
    

});

// $('.save-questions').click(function (event) { 

//     var err=0;
//     $('.question-list').find('select').each(function () { 

//         if($(this).val()!='')
//         { 
//             var i = $(this).closest(".question").attr("row-count");
//             var question = $(this).closest(".question").find("input[name='question["+i+"]']").val();
//             var title = $(this).closest(".question").find("input[name='title["+i+"]']").val();

            
//             if(title=='')
//             {
//                 alert("Please Enter Title");
//                 err++;
//             }
//             else if(question=='')
//             {
//                 alert("Please Enter Question");
//                 err++;
//             }
//             else if(!validateInputOptions($(this)))
//             {
//                 alert("please enter alteast one option and score for question "+title);
//                 err++;
//             }
//         }
//     });

    
//     // var questionType = $(".question:last").find("select[name='questionType["+i+"]']").val();
    

//     // if(questionType!='')
//     // {
//     //     if(!validateInputOptions($(".question:last").find("select[name='questionType["+i+"]']")))
//     //     {
//     //         alert("please enter alteast one option and score");
//     //         return;
//     //     }
//     //     else if(title=='')
//     //     {
//     //         alert("Please Enter Title");
//     //         return;
//     //     }
//     //     else if(question=='')
//     //     {
//     //         alert("Please Enter Question");
//     //         return;
//     //     }
//     // }
    
//     if(err==0)   
//         $('form').submit();
     
       
     
    

// });

/*******New Questionnaire script*******/

function hideEditButtons(Obj)
{
    Obj.closest(".question-view-edit").find(".question-view").addClass("hidden");
    Obj.closest(".question-view-edit").find(".question-edit").removeClass("hidden");

    $('.questions-list_container').find('.edit-question').each(function () { 
        $(this).addClass("hidden");
    });

    $('.add-question').addClass("hidden");
}

function showEditButtons(Obj)
{
    Obj.closest(".question-view-edit").find(".question-view").removeClass("hidden");
    Obj.closest(".question-view-edit").find(".question-edit").addClass("hidden");
    $('.questions-list_container').find('.edit-question').each(function () { 
        $(this).removeClass("hidden");
    });
    $('.add-question').removeClass("hidden");
}

function showNoQuestionMsg()
{
    if(!$('.questions-list_container').find('form').length)
    {
        $('.questions-list_container').find('.no_question').removeClass('hidden'); 
        $('.questionnaire-settings').addClass('hidden');
        $('.question-reorder').addClass('hidden');
        $('.publish-question').addClass('hidden');
        $('.questions-list__header').addClass('hidden');

        $('.add-question').text('Add Question');
    }
}
 
$('.questions-list_container').on('click', '.edit-question', function(event) { 
    hideEditButtons($(this));
});
 
$('.questions-list_container').on('click', '.cancel-question', function(event) { 

    var i = $(this).closest(".question").attr('row-count'); 
    var questionId = $(this).closest('.question-view-edit').find('input[name="questionId['+i+']"]').val();  
    showEditButtons($(this));

    if(questionId=='')
    { 
        $(this).closest('form').remove();
    }

    showNoQuestionMsg();
     
});


 
$('.questions-list_container').on('click', '.toggle-subquestion', function(event) { 

    if($(this).hasClass('hideSubquestion'))
    {
        
        $(this).text('SHOW SUB QUESTION');
        $(this).closest('.options-list_container').find('.subquestion-container').addClass("hidden");
        $(this).removeClass('hideSubquestion');
    }
    else
    {
        $(this).text('HIDE SUB QUESTION');
        $(this).closest('.options-list_container').find('.subquestion-container').removeClass('hidden');
        $(this).addClass('hideSubquestion');

        if(!$(this).closest('.options-list_container').find('.subquestion-error-message').hasClass('hidden'))
            $(this).closest('.options-list_container').find('.subquestion-error-message').addClass('hidden')
        
    }
 
});

$('.add-question').click(function (event) { 

    hideEditButtons($(this));
    
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;
    var j = $(".question:last").attr("row-count");
 
    html ='<form class="form-horizontal col-sm-12 p-l-0 p-r-0" method="post" action="'+ submitUrl +'" data-parsley-validate>';
    html +='<div class="question-view-edit">';
    html +='<div class="row questions-list hidden question-view question" row-count="'+i+'">';
    html +='<div class="col-sm-3">';
    html +='<div class="black question-title"></div>';
    html +='<div class="type question-type"></div>';
    html +='</div>';
    html +='<div class="col-sm-4">';
    html +='<div class="bold question-text"></div>';
    html +='</div>';
    html +='<div class="col-sm-1">';
    html +='<div class="text-center question-option-count"></div>';
    html +='</div>';
    html +='<div class="col-sm-2">';
    html +='<div class="text-center has-subquestion">';

    html +='</div>';
    html +='</div>';
    html +='<div class="col-sm-2">';
    html +='<div class="clearfix">';
    html +='<input type="hidden" name="previousquestionId['+i+']" value="">';
    html +='<input type="hidden" name="questionId['+i+']" value="">';
    html +='<span class="pull-left edit-link edit-question">EDIT</span>';
    html +='<i class="pull-right fa fa-trash delete-parent-question delete-question" object-id=""></i>';
    html +='</div>';
    html +='</div>';
    html +='</div> ';
    html +='<div class="main-question_container question-edit question" row-count="'+i+'">';
    html +='<div class="type-questions parentQuestion">';
    html +='<div class="row">';
    html +='<div class="col-sm-3">';
    html +='<div class="form-group">';
    html +='<input type="hidden" name="questionType['+i+']" >';
    html +='<label for="">Type of question</label>';
    html +='<select name="questionType['+i+']" class="select2-container select2 form-control questionType" data-parsley-required>';
    html +='<option value="">Select Question Type</option>';
    html +='<option value="single-choice"> Single-choice</option>';
    html +='<option value="multi-choice">Multi-choice</option>';
    html +='<option value="input"> Input</option>';
    html +='<option value="descriptive"> Descriptive </option>';
    html +='<option value="input" data-value="weight"> Weight </option>';
    html +='</select>';

    html +='</div>';
    html +='</div>';
    html +='<div class="col-sm-4">';
    html +='<div class="form-group">';
    html +='<label for="">A short question identifier</label>';
    html +='<input name="title['+i+']" id="title" type="text"   placeholder="Enter Title" class="form-control" data-parsley-required>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-sm-1">';
    html +='<div class="text-center question-option-count"></div>';
    html +='</div>';
    html +='<div class="col-sm-2">';
    html +='<div class="text-center has-subquestion"></div>';
    html +='</div>';
    html +='<div class="col-sm-2">';
    html +='<i class="fa fa-trash text-danger delete-parent-question delete-question pull-right cp" object-id=""></i>';
    html +='</div>';
    html +='</div>';
    html +='<div class="row">';
    html +='<div class="col-md-9">';
    html +='<div class="form-group">';
    html +='<input name="question['+i+']" id="question" type="text"   placeholder="Enter Question" class="form-control" data-parsley-required>';
    html +='</div>';
    html +='</div>';
    html +='</div>';
    html +='</div> ';
    html +='<div class="options-container parent-question-options question-options-block hidden ">';
    html +='<div class="row heading-title m-b-15">';
    html +='<div class="col-md-12">';
    html +='<span class="bold">Enter the options for this question</span>';
    html +='<div>You can add a subquestion too. The score declairs severity of patient.</div>';
    html +='</div>';
    html +='</div>';
    html +='</div>';
    html +='<div class="row options-container_footer">';
    html +='<div class="col-md-12">';
    html +='<div class="clearfix">';
    html +='<button type="button"  class="btn btn-primary pull-right save-question">SAVE</button>';
    html +='<button type="button" class="btn btn-default pull-right cancel-question m-r-10">CANCEL</button>';
    html +='</div>';
    html +='</div>';
    html +='</div>';
    html +='</div> ';
    html +='</div>';
    html +='</form>';
    
    $('.questions-list__header').removeClass('hidden');
    if($('.questions-list_container').find('.no_question').length)
        $('.questions-list_container').find('.no_question').addClass('hidden'); 

    $('.questions-list_container').append(html);
    $('input[name="counter"]').val(i);
     

});

$('.questions-list_container').on('click', '.delete-question', function(event) { 
    
    if (confirm('Are you sure you want to delete this question?') === false) {
        return;
    }
    var Obj = $(this);

    var i = Obj.closest(".question").attr('row-count');  
    

    Obj.closest('div').append('<span class="cf-loader"></span>');

    if(!Obj.hasClass('delete-parent-question'))
    {  
        Obj.closest('.options-list_container').find('.hasSubQuestion').trigger('click');
        var questionId = Obj.closest(".question").find('input[name="questionId['+i+']"]').val();
    }
    else
        var questionId = Obj.closest(".question-view-edit").find('input[name="questionId['+i+']"]').val();
        

    if(questionId!='')
    {
        $.ajax({
            url: BASEURL + "/delete-question/" + questionId,
            type: "DELETE",
            success: function (response) {

                if(!Obj.hasClass('delete-parent-question'))
                {  
                    Obj.closest('.sub-question').find('.toggle-subquestion').after('<span  class="add-link add-sub-question p-l-20 cp">ADD SUB QUESTION</span>');
                    Obj.closest('.sub-question').find('.toggle-subquestion').remove();
                    Obj.closest('.question').remove();
                }
                else
                {
                    Obj.closest('form').remove();
                    showNoQuestionMsg();
                }
                  

            }
                 
        });
    }
    else
    {
        if(!Obj.hasClass('delete-parent-question'))
        {  
            
            Obj.closest('.sub-question').find('.toggle-subquestion').after('<span  class="add-link add-sub-question p-l-20 cp">ADD SUB QUESTION</span>');
            Obj.closest('.sub-question').find('.toggle-subquestion').remove();
            Obj.closest('.question').remove();
            
        }
        else
        {
            Obj.closest('form').remove();
            showNoQuestionMsg();
        }
    }

    

    showEditButtons($(this));
    

});


$('.questions-list_container').on('change', '.questionType', function(event) { 

    var counter = $(this).closest(".question").attr("row-count");
    var i = parseInt(counter); 
    var j = 0;

    var html = '';
    $(this).closest('.question').find('.question-options-block').html(html);

    if($(this).hasClass('subquestionType'))
    {
        html +='<span class="bold m-t-15">Enter the option for this sub question</span>';
    }
    else if(($(this).val()=='single-choice') || ($(this).val()=='multi-choice') || ($(this).val()=='input'))
    {
        var questionType = $(this).val();

        html +='<div class="row heading-title m-b-15">';
        html +='<div class="col-md-12">';
        html +='<span class="bold">Enter the options for this question</span>';
        if(questionType=='single-choice')
            html +='<div>You can add a subquestion too. The score declares severity of the option.</div>';
        html +='</div>';
        html +='</div>';
    }

    if($(this).val()=="input" && $('option:selected', this).attr("data-value")=="weight")
    {
        $(this).closest('.question').find('.question-options-block').addClass('hidden');
        html +='<div class="options-list_container m-b-5">';
        html +='<div class="row options-list">';
        html +='<div class="col-sm-2 option-label">';
        html +='<input type="hidden" name="optionId['+i+'][0]"  class="optionId" value="">';
        html +='<label for="" class="m-t-10">option 1</label>';
        html +='</div>';
        html +='<div class="col-sm-4">';
        html +='<input name="option['+i+'][0]" id="question" type="hidden" placeholder="Enter option" value="kg" class="form-control" >';
        html +='</div>';
        html +='<div class="col-sm-1 text-right">';
        html +='<label for="" class="m-t-10">score</label>';
        html +='</div>';
        html +='<div class="col-sm-1">';
        html +='<input name="score['+i+'][0]" id="question" type="hidden" placeholder="Enter score" value="1" class="form-control" >';
        html +='</div>';

        html +='<div class="col-sm-4  add-delete-container">';
        html +='</div>';
        html +='</div>';
        html +='</div>';

        html +='<div class="options-list_container m-b-5">';
        html +='<div class="row options-list">';
        html +='<div class="col-sm-2 option-label">';
        html +='<input type="hidden" name="optionId['+i+'][1]" value="">';
        html +='<label for="" class="m-t-10">option 2</label>';
        html +='</div>';
        html +='<div class="col-sm-4">';
        html +='<input name="option['+i+'][1]" id="question" type="hidden" placeholder="Enter option" value="st" class="form-control" >';
        html +='</div>';
        html +='<div class="col-sm-1 text-right">';
        html +='<label for="" class="m-t-10">score</label>';
        html +='</div>';
        html +='<div class="col-sm-1">';
        html +='<input name="score['+i+'][1]" id="question" type="hidden" placeholder="Enter score" value="2" class="form-control" >';
        html +='</div>';

        html +='<div class="col-sm-4  add-delete-container">';
        html +='</div>';
        html +='</div>';
        html +='</div>';

        html +='<div class="options-list_container m-b-5">';
        html +='<div class="row options-list">';
        html +='<div class="col-sm-2 option-label">';
        html +='<input type="hidden" name="optionId['+i+'][2]" value="">';
        html +='<label for="" class="m-t-10">option 3</label>';
        html +='</div>';
        html +='<div class="col-sm-4">';
        html +='<input name="option['+i+'][2]" id="question" type="hidden" placeholder="Enter option" value="lb" class="form-control" >';
        html +='</div>';
        html +='<div class="col-sm-1 text-right">';
        html +='<label for="" class="m-t-10">score</label>';
        html +='</div>';
        html +='<div class="col-sm-1">';
        html +='<input name="score['+i+'][2]" id="question" type="hidden" placeholder="Enter score" value="3" class="form-control" >';
        html +='</div>';

        html +='<div class="col-sm-4  add-delete-container">';
        html +='</div>';
        html +='</div>';
        html +='</div>';

    }
    else if($(this).val()=="single-choice" || $(this).val()=="multi-choice" || $(this).val()=="input")
    {
        var isSubQuestionOption = ($(this).hasClass('subquestionType'))?'yes':'no'; 
        var hasSubQuestion = ($(this).val()=="single-choice" && !$(this).hasClass('subquestionType'))?1:0;  

        html += getOptionHtml(isSubQuestionOption, hasSubQuestion,'data-parsley-required', i, 0)
        $(this).closest('.question').find('.question-options-block').removeClass('hidden');
    }

    
    $(this).closest('.question').find('.question-options-block').html(html);
    

        
});

$('.questions-list_container').on('click', '.add-sub-question', function(event) { 

    $(this).closest('.row').find('.hasSubQuestion').attr('checked','checked');

    var optionKey1 = $(this).closest('.row').find('.hasSubQuestion').prop('name').match(/\[(.*?)\]\[(.*?)\]/)[1];
    var optionKey2 = $(this).closest('.row').find('.hasSubQuestion').prop('name').match(/\[(.*?)\]\[(.*?)\]/)[2]; 


    var counter = $('input[name="counter"]').val(); 
    var i = parseInt(counter) + 1;



        html ='<span class="sh-link toggle-subquestion hideSubquestion cp p-l-20">HIDE SUB QUESTION</span> <span class="subquestion-error-message alert alert-danger cust-alert-padd hidden"><i class="fa fa-exclamation-triangle"></i> Please fill required fields for these sub-question</span>';
        html +='<div class="subquestion-container question" row-count="'+i+'">';
        html +='<input type="hidden" name="questionId['+i+']" value="">';
        html +='<div class="clearfix">';
        html +='<span class="bold pull-left">Edit this Subquestion</span>';
        html +='<span class="fa fa-trash text-danger pull-right delete-question" object-id=""></span>';
        html +='</div>';

        html +='<div class="type-questions">';
        html +='<div class="row">';
        html +='<div class="col-sm-3">';
        html +='<div class="form-group">';
        html +='<input type="hidden" name="subquestionType['+i+']" >';
        html +='<label for="">Type of question</label>';
        html +='<input type="hidden" name="optionKeys['+optionKey1+']['+optionKey2+']" value="'+i+'">';
        html +='<select name="subquestionType['+i+']" class="select2-container select2 form-control  subquestionType questionType" data-parsley-required>';
        html +='<option value="">Select Question Type</option>';
        html +='<option value="single-choice"> Single-choice</option>';
        html +='<option value="multi-choice">Multi-choice</option>';
        html +='<option value="input"> Input</option>';
        html +='<option value="descriptive"> Descriptive </option>';
        html +='<option value="input" data-value="weight"> Weight </option>';
        html +='</select>';
        html +='</div>';
        html +='</div>';
        html +='<div class="col-sm-4">';
        html +='<div class="form-group">';
        html +='<label for="">A short question identifier</label>';
        html +='<input name="subquestionTitle['+i+']" id="subquestionTitle" type="text"   placeholder="Enter Title" class="form-control" data-parsley-required>';
        html +='</div>';
        html +='</div>';
        html +='</div>';

        html +='<div class="row">';
        html +='<div class="col-md-9">';
        html +='<div class="form-group">';
        html +='<input name="subquestion['+i+']" id="subquestion" type="text"   placeholder="Enter Question" class="form-control" data-parsley-required>';
        html +='</div>';
        html +='</div>';
        html +='</div>';
        html +='</div> ';

        html +='<div class="question-options-block hidden">';
        // html +='<span class="bold m-t-15">Enter the option for this sub question</span>';

        html +='</div> ';

        html +='</div>';
       
    $('input[name="counter"]').val(i);
 

    $(this).closest('.sub-question').html(html);
    
    

});


 
$('.questions-list_container').on('click', '.save-question', function(event) {  
    var Obj = $(this);
    var form = Obj.closest('form');

    form.parsley().validate();

    url = form.attr("href");
    if (form.parsley().isValid()){
        $.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           success: function(response)
           {  
                
                var questionType = (response.data.questionType).toUpperCase();
                Obj.closest(".question-view-edit").find(".question-view").find(".question-type").text('TYPE: '+questionType);
                Obj.closest(".question-view-edit").find(".question-view").find(".question-text").text(response.data.question);
                Obj.closest(".question-view-edit").find(".question-view").find(".question-title").text(response.data.title);
                Obj.closest(".question-view-edit").find(".question-view").find(".question-option-count").text(response.data.questioOptionCount);
                Obj.closest(".question-view-edit").find(".question-view").find(".has-subquestion").text(response.data.hasSubQuestion);
                
                
                showEditButtons(Obj);
                 
                var responseOptionIds = response.data.responseOptionIds;
                var responseQuestionIds = response.data.responseQuestionIds;
                
               
                $.each(responseOptionIds, function (index1, array) {   
  
                    Obj.closest(".question-view-edit").find("input[name='questionId["+index1+"]']").val(responseQuestionIds[index1]);
                    
                    if(Obj.closest('.question-view-edit').find('select[name="questionType['+index1+']"]').length)
                    {
                        var questionType = Obj.closest('.question-view-edit').find('select[name="questionType['+index1+']"]').val();
                        Obj.closest('.question-view-edit').find('input[name="questionType['+index1+']"]').val(questionType); 
                        Obj.closest('.question-view-edit').find('select[name="questionType['+index1+']"]').attr('disabled','disabled') 
                    }
                    else
                    {   //sub-question
                        var subquestionType = Obj.closest('.question-view-edit').find('select[name="subquestionType['+index1+']"]').val();
                        Obj.closest('.question-view-edit').find('input[name="subquestionType['+index1+']"]').val(subquestionType); 
                        Obj.closest('.question-view-edit').find('select[name="subquestionType['+index1+']"]').attr('disabled','disabled') 
                    }
                   

                    $.each(array, function (index2, value) {  
                        Obj.closest(".question-view-edit").find("input[name='optionId["+index1+"]["+index2+"]']").val(value);

                        
                         
                    });

                }); 

                // show buttons
                if($('.questionnaire-settings').hasClass('hidden'))
                {
                    $('.questionnaire-settings').removeClass('hidden');
                    $('.question-reorder').removeClass('hidden');
                    $('.publish-question').removeClass('hidden');
                 

                    $('.add-question').text('Add another Question');
                }

                //keep last option empty
                optionCount=Obj.closest('.question-view-edit').find('.options-list_container').length;
                console.log(optionCount);
               
           }
         });
    }
    else
    {
        //notify errors in hidden container 
        form.find('.subquestion-container').filter('.hidden').each(function () { 
            if($(this).find('.parsley-required').length)  
            {
                $(this).closest('.sub-question').find('.subquestion-error-message').removeClass('hidden');
            }
            else
            {
                $(this).closest('.sub-question').find('.subquestion-error-message').addClass('hidden');
            }

        });
    }


});


$('.questions-list_container').on('click', '.add-option', function(event) { 
    
    var counter = $(this).closest(".question").attr("row-count");
    var i = parseInt(counter);
    

    var counterKey = $(this).attr("counter-key");
    var j = parseInt(counterKey) + 1;

    var question = $(this).closest(".row").find("input[name='option["+i+"]["+counterKey+"]']").val();
    var score = $(this).closest(".row").find("input[name='score["+i+"]["+counterKey+"]']").val();

    //add validation 
    $(this).closest(".row").find("input[name='option["+i+"]["+counterKey+"]']").attr('data-parsley-required', 'true');
    $(this).closest(".row").find("input[name='score["+i+"]["+counterKey+"]']").attr('data-parsley-required', 'true');

    var containerObj = $(this).closest(".add-delete-container");
    containerObj.html('<i class="fa fa-remove m-t-10 delete-option cp" counter-key="'+counterKey+'"></i>');
    
    if(containerObj.closest('.question-options-block').hasClass('parent-question-options'))
    {
        var hasSubQuestion = containerObj.closest(".question-options-block").find(".hasSubQuestion").length;
        var html = getOptionHtml('no', hasSubQuestion,'', i, j);
    }
    else
    {
        var html = getOptionHtml('yes', 0,'', i, j);
    }

    containerObj.closest('.options-list_container').after(html);

      

});

function getOptionHtml(isSubQuestionOption, hasSubQuestion,required, i, j)
{
   
    if(isSubQuestionOption=='no')
    {  
        //parent question Option html
        html ='<div class="options-list_container p-b-10">';
        html +='<div class="row options-list">';
        html +='<div class="col-sm-1 cust-col-sm-1">';
        html +='<label for="" class="m-t-10">option '+ (j+1) +'</label>';
        html +='<input type="hidden" name="optionId['+i+']['+j+']" class="optionId"  value="">';
        html +='</div>';
        html +='<div class="col-sm-4">';
        html +='<input name="option['+i+']['+j+']" id="option" type="text" placeholder="Enter option" '+required+' class="form-control" >';
        html +='</div>';

        html +='<div class="col-sm-1 text-right">';
        html +='<label for="" class="m-t-10">score</label>';
        html +='</div>';
        html +='<div class="col-sm-2 cust-col-sm-2">';
        html +='<input name="score['+i+']['+j+']" id="score" type="number" placeholder="Enter score" value="0" '+required+' class="form-control" min="0" >';
        html +='</div>';

        html +='<div class="col-sm-4 add-delete-container">';
        html +='<div class="clearfix">';
        html +='<span class="btn btn-default pull-right outline-btn-gray add-option" counter-key="'+j+'">Another option <i class="fa fa-plus"></i></span>';
        html +='</div>';
        html +='</div>';
                         
        html +='</div>';
        html +='<div class="row">';
        if(hasSubQuestion)
        {
            html +='<input type="checkbox" class="hidden hasSubQuestion" name="hasSubQuestion['+i+']['+j+']" />';
            html +='<div class="col-sm-11 col-sm-offset-1 sub-question">';
            html +='<span  class="add-link add-sub-question p-l-20 cp">ADD SUB QUESTION</span>';
            html +='</div>';
        }
        html +='</div>';
        html +='</div>';
    }
    else{ 

         // sub question option html
        html ='<div class="options-list_container m-b-5">';
        html +='<div class="row options-list">';
        html +='<div class="col-sm-2 option-label">';
        html +='<input type="hidden" name="optionId['+i+']['+j+']"  class="optionId" value="">';
        html +='<label for="" class="m-t-10">option '+ (j+1) +'</label>';
        html +='</div>';
        html +='<div class="col-sm-4">';
        html +='<input name="option['+i+']['+j+']" id="option" type="text" placeholder="Enter option" '+required+'  class="form-control" >';
        html +='</div>';
        html +='<div class="col-sm-1 text-right">';
        html +='<label for="" class="m-t-10">score</label>';
        html +='</div>';
        html +='<div class="col-sm-2">';
        html +='<input name="score['+i+']['+j+']" id="score" type="number" placeholder="Enter score" value="0" '+required+' class="form-control" min="0" >';
        html +='</div>';

        html +='<div class="col-sm-3  add-delete-container">';
        html +='<span class="btn btn-default pull-right outline-btn-gray add-option" counter-key="'+j+'">Another option <i class="fa fa-plus"></i></span>';
        html +='</div>';
        html +='</div>';
        html +='</div>';
    }

    return html;
}


$('.questions-list_container').on('click', '.delete-option', function(event) { 
    
    

    var Obj = $(this);
    var i = $(this).closest('.question').attr("row-count"); 
    var counterKey = $(this).attr("counter-key");
    var optionId = Obj.closest(".row").find('input[name="optionId['+i+']['+counterKey+']"]').val();  
    
    
    var count= 0; 
    var title = '';
    var i= 0;

    if(Obj.closest('.question').hasClass("main-question_container"))
    {  
        count=getOptionsCount(Obj);
        i = Obj.closest('.main-question_container').attr("row-count");
        title = Obj.closest('.main-question_container').find('input[name="title['+i+']"]').val(); 
    }
    else
    {  
        count=Obj.closest('.subquestion-container').find('.options-list_container').length;
        i = Obj.closest('.subquestion-container').attr("row-count"); 
        title = Obj.closest('.subquestion-container').find('input[name="subquestionTitle['+i+']"]').val();
    }


    if(count > 2)
    {
        if (confirm('Are you sure you want to delete this option?') === false) {
            return;
        }

        Obj.closest('div').append('<span class="cf-loader"></span>');
        if(optionId!='')
        {
            $.ajax({
                url: BASEURL + "/delete-option/" + optionId,
                type: "DELETE",
                success: function (response, status, xhr) { 
                 
                    if(xhr.status==203)
                        Obj.closest('.options-list_container').remove(); 
                
                }
            });
        }
        else
        {
            Obj.closest('.options-list_container').remove(); 
        }
    }
    else
    {
        alert("Please make sure at least one option is present for question "+title+".");
    }


});

/************/


function validateInputOptions(inputTypeObject)
{
    var i = $(inputTypeObject).closest(".question").attr("row-count");  
    var firstOption = $(inputTypeObject).closest(".question").find('input[name="option['+i+'][0]"]').val();  
    var firstOptionScore = $(inputTypeObject).closest(".question").find('input[name="score['+i+'][0]"]').val(); 

    var flag = true;

    if(firstOption=="" || firstOptionScore=="")
     {  
        flag = false;
     }           
    

    return flag;
     
}

function validatefrequencySettings(frequencyRequired)
{  
    var flag =  true;
    var frequencyDay = $('input[name="frequencyDay"]').val();
    frequencyDay = (frequencyDay=='') ? 0 : parseInt(frequencyDay);
    var frequencyHours = $('input[name="frequencyHours"]').val();
    frequencyHours = (frequencyHours=='')? 0 : parseInt(frequencyHours);  
    var totalFrequencyHours = (frequencyDay*24) + frequencyHours;  

    var expectedGracePeriod = parseInt(totalFrequencyHours)/2; 

    var gracePeriodDay = $('input[name="gracePeriodDay"]').val();
    gracePeriodDay = (gracePeriodDay=='') ? 0 : parseInt(gracePeriodDay);
    var gracePeriodHours = $('input[name="gracePeriodHours"]').val();
    gracePeriodHours = (gracePeriodHours=='') ? 0 : parseInt(gracePeriodHours);

    var totalGPHours = (gracePeriodDay*24) + gracePeriodHours; 

    var reminderTimeDay = $('input[name="reminderTimeDay"]').val();
    reminderTimeDay = (reminderTimeDay=='') ? 0 : parseInt(reminderTimeDay);
    var reminderTimeHours = $('input[name="reminderTimeHours"]').val();
    reminderTimeHours = (reminderTimeHours=='') ? 0 : parseInt(reminderTimeHours);

    var totalRTHours = (reminderTimeDay*24) + reminderTimeHours;

    if(frequencyRequired)
    {
        if(totalFrequencyHours==0)
        {
            $('input[name="frequencyHours"]').closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Please Enter Frequency</li>');
            flag = false;
        }

        if(totalGPHours==0)
        {
         
            $('input[name="gracePeriodHours"]').closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Please Enter Grace peroid</li>');
            flag = false;
        }

        if(totalRTHours==0)
        {
            $('input[name="reminderTimeHours"]').closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Please Enter Reminder Time</li>');
            flag = false;
        }

        
    }

    if(totalFrequencyHours > 0)
    {
        $('input[name="frequencyHours"]').closest('div').find('.parsley-errors-list').html('');
        $('input[name="gracePeriodHours"]').closest('div').find('.parsley-errors-list').html('');
        $('input[name="reminderTimeHours"]').closest('div').find('.parsley-errors-list').html('');
        if(totalFrequencyHours>=1 && totalGPHours==0)
        {
         
            $('input[name="gracePeriodHours"]').closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Please Enter Grace peroid</li>');
            flag = false;
        }
        else if(totalGPHours >= expectedGracePeriod)
        {
      
            $('input[name="gracePeriodHours"]').closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Grace peroid should be less then '+ expectedGracePeriod.toString() +' hours</li>');
            flag = false;
        }

        if(totalGPHours>=1 && totalRTHours==0)
        {
            $('input[name="reminderTimeHours"]').closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Please Enter Reminder Time</li>');
            flag = false;
        }
        else if(totalRTHours>=totalGPHours)
        {
            $('input[name="reminderTimeHours"]').closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Reminder time should be less then '+ totalGPHours.toString() +' hours</li>');
            flag = false;
        }
    }

    return flag;
     
}




