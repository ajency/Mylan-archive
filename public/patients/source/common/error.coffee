angular.module 'angularApp.common'


.directive 'ajError', [->

	restrict: 'E'
	replace: true
	templateUrl: 	'patients/views/error.html'

	scope:
		tapToRetry: '&'
		errorType: '='
		setTy: '='
	
	link: (scope, el, attr)->

		switch scope.errorType
			when 'server_connection'
				errorMsg = 'Could not connect to server'
			when 'server_error'
				errorMsg = 'Server error'
			when 'offline'
				errorMsg = 'No internet availability'
			else
				errorMsg = 'Unknown error'

		
		scope.errorMsg = errorMsg

		scope.onTryAgain = ->
			scope.tapToRetry()

]