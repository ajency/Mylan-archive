app = angular.module 'PatientApp.Global'

.directive 'textSelect', ['$timeout', ($timeout)->

  link: (scope, element, attr)->
    # $('input').keyup (e) ->
    #   console.log 'onkey uppp'
    #   if $(this).val() != ''
    #     $('input').not(this).attr 'disabled', 'disabled'
    #   else
    #     $('input').removeAttr 'disabled'

    # $('#numberType').keyup (e) ->
    #  	console.log 'onkeyupp'

   	# $('#numberType input[type=text]').each ->
   	# 	aw
   	# 	a = $(this).val()
   	# 	console.log '***'
   	# 	console.log a

	  
    

]

.directive 'mcqSelect', ['$timeout', ($timeout)->
  
  link: (scope, element, attr)->
    $timeout ->
      $(element).click ->
        # ng-click="view.deleteNotify(notify.id)" 
        # if $(this).hasClass('mcq_active')
        #   $(this).removeClass('mcq_active')
        $(element).parent().addClass('mcq_a')


    
]