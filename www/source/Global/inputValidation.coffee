app = angular.module 'PatientApp.Global'

.directive 'textSelect', ['$timeout', ($timeout)->

  link: (scope, element, attr)->
    $('input').keyup (e) ->
      console.log 'onkey uppp'
      if $(this).val() != ''
        $('input').not(this).attr 'disabled', 'disabled'
      else
        $('input').removeAttr 'disabled'

    # $('#numberType').keyup (e) ->
    #  	console.log 'onkeyupp'

   	# $('#numberType input[type=text]').each ->
   	# 	aw
   	# 	a = $(this).val()
   	# 	console.log '***'
   	# 	console.log a

	  
    

]