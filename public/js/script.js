$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


var uploader = new plupload.Uploader({
    runtimes : 'html5,flash,silverlight,html4',
     
    browse_button : 'pickfiles', // you can pass in id...
    container: document.getElementById('hospital_logo'), // ... or DOM Element itself
     
    url: '/admin/hospital/'+HOSPITAL_ID+'/media/uploadlogo',
    
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
        },
 
        UploadProgress: function(up, file) {
            document.getElementById('loader').innerHTML = '<span>' + file.percent + "%</span>";
        },

        FileUploaded: function (up, file, xhr) {
            fileResponse = JSON.parse(xhr.response);

            var Img = '<img src="'+ fileResponse.data.image_path +'">';
            
            $('#hospital_logo').val(fileResponse.data.filename);
            $('#hospital_logo_block').html(Img);
 

         },
 
        Error: function(up, err) {
            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
        }
    }
});
 
uploader.init();