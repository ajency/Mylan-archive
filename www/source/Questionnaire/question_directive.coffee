angular.module 'PatientApp.Quest'

.directive 'mcqSelect', ['$timeout', ($timeout)->
	
	link: (scope, element, attr)->
		$timeout ->
			$('.mcq').click ->
				if $(this).hasClass('mcq_active')
					$(this).removeClass('mcq_active')
				else
					$(this).addClass('mcq_active')


		
]