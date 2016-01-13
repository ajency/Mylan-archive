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
                alert('Reference Code Already Taken');
                $("#reference_code").val('');
            }

            // $(".cf-loader").addClass('hidden');
        }
    });
    
 
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
            $('#loader').removeClass('hidden');
        },
 
        UploadProgress: function(up, file) {
            document.getElementById('loader').innerHTML = '<span>' + file.percent + "%</span>";
        },

        FileUploaded: function (up, file, xhr) {
            fileResponse = JSON.parse(xhr.response);
            $('#pickfiles').addClass('hidden');
            var imgStr = '<img src="'+ fileResponse.data.image_path +'" class="img-responsive">';
            imgStr += '<a class="deleteHospitalLogo" data-type="hospital" data-value="'+HOSPITAL_ID+'" href="javascript:;">[delete]</a>';
            $('#hospital_logo').val(fileResponse.data.filename);
            $('#hospital_logo_block').html(imgStr);
            $('#loader').addClass('hidden');

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
        $('#hospital_logo_block').html('');
        $('#pickfiles').removeClass('hidden');
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
                $('#hospital_logo_block').html('');
                $('#pickfiles').removeClass('hidden');
            }
        });
    }

    

});

$('.add-hospital-user').click(function (event) { 

    var objectType = $(this).attr('object-type');

    if($(".hospital_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
        return;
    }

    var addHospital = $(".hospital_users:last").find('select').html(); 
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;

    html ='<hr><div class="row hospital_users">';
    html +='<div class="col-md-3">';
    html +='<input type="hidden" name="user_access[]" value="">';
    html +='<select name="hospital[]" id="hospital" class="select2 form-control"  >';
    html += addHospital
    html +='<select>';
    html +='</div>';
               
    html +='<div class="col-md-3">';
    html +='<div class="radio radio-primary">';
    html +='<input id="access_view_'+i+'" type="radio" name="access_'+i+'" value="view" checked="checked">';
    html +='<label for="access_view_'+i+'">View</label>';
    html +='<input id="access_edit_'+i+'" type="radio" name="access_'+i+'" value="edit">';
    html +='<label for="access_edit_'+i+'">Edit</label>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-md-3">';
    html +='<a class="deleteUserHospitalAccess hidden"> delete </a>';
    html +='</div>';
    html +='</div>';

    $('input[name="counter"]').val(i);
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
    html +='<div class="col-sm-3 m-t-25 input-daterange">';
    html +='<input name="visitDate[]" id="visitDate" type="text"  class="form-control" placeholder="Enter Date" >';
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

    $(".patient-visit:last").find('input').datepicker({
         format: 'dd-mm-yyyy'
     });

});


$('.visit-data').on('click', '.delete-visit', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    $(this).closest('.patient-visit').remove();
});

$('.add-project-user').click(function (event) { 

    var objectType = $(this).attr('object-type');

    if($(".project_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
        return;
    }

    var addProjects = $(".project_users:last").find('select').html(); 
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;

    html ='<hr><div class="row project_users">';
    html +='<div class="col-md-3">';
    html +='<input type="hidden" name="user_access[]" value="">';
    html +='<select name="projects[]" id="projects" class="select2 form-control"  >';
    html += addProjects
    html +='<select>';
    html +='</div>';
               
    html +='<div class="col-md-3">';
    html +='<div class="radio radio-primary">';
    html +='<input id="access_view_'+i+'" type="radio" name="access_'+i+'" value="view" checked="checked">';
    html +='<label for="access_view_'+i+'">View</label>';
    html +='<input id="access_edit_'+i+'" type="radio" name="access_'+i+'" value="edit">';
    html +='<label for="access_edit_'+i+'">Edit</label>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-md-3">';
    html +='<a class="deleteUserProjAccess hidden"> delete </a>';
    html +='</div>';
    html +='</div>';

    $('input[name="counter"]').val(i);
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

$('.deleteUserHospitalAccess').click(function (event) { 
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

