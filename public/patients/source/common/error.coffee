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
			when 'offline'
				errorMsg = 'No internet availability'
			when 'server_error'
				errorMsg = 'Server error'
			else
				errorMsg = 'Unknown error'

		
		scope.errorMsg = errorMsg

		scope.onTryAgain = ->
			scope.tapToRetry()

]