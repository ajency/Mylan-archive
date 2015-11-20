angular.module 'PatientApp.Global'


.directive 'ajError', [->

	restrict: 'E'
	replace: true
	templateUrl: 	'views/error-view/error.html'

	scope:
		tapToRetry: '&'
		errorType: '='
	
	link: (scope, el, attr)->
		console.log scope.errorType
		# switch scope.errorType
		# 	when 'offline'
		# 		errorMsg = 'No internet availability'
		# 	when 'server_error'
		# 		errorMsg = 'Could not connect to server'
		# 	when 'session_expired'
		# 		errorMsg = 'Your session has expired'
		# 	else
		# 		errorMsg = 'Unknown error'
		
		# scope.errorMsg = errorMsg

		# scope.onTryAgain = ->
		# 	scope.tapToRetry()
]

