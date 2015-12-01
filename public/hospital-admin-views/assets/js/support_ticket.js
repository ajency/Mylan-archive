
/* Webarch Admin Dashboard 
/* This JS is only for DEMO Purposes - Extract the code that you need
-----------------------------------------------------------------*/ 
$(document).ready(function(){	
/** Text Editor **/
//$('#message').wysihtml5();	

/** Events **/
$('#btn-new-ticket').click( function() {
	$('#new-ticket-wrapper').slideToggle("fast","linear")
})

$('#btn-close-ticket').click( function() {
	$('#new-ticket-wrapper').slideToggle("fast","linear")
});

$('#new-ticket-form').validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    txtSubject: {
                        minlength: 2,
                        required: true
                    },
                    txtDept: {
						minlength: 2,
                        required: true,
                    },
                    txtMessage: {
                        required: true
                    }
                },

                invalidHandler: function (event, validator) {
					//display error alert on form submit    
                },

               

			    submitHandler: function(form) {
					$('#new-ticket-wrapper').slideToggle("fast","linear");
				}
            });	

	$('.grid .actions a.remove').on('click', function () {
            var removable = jQuery(this).parents(".grid");
            if (removable.next().hasClass('grid') || removable.prev().hasClass('grid')) {
                jQuery(this).parents(".grid").remove();
            } else {
                jQuery(this).parents(".grid").parent().remove();
            }
    });

     $('.grid .clickable').on('click', function () {
          var el = jQuery(this).parents(".grid").children(".grid-body");
		  el.slideToggle(200);
    });		
});