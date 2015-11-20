angular.module 'PatientApp.Global'


.directive 'ajError', [->

	restrict: 'E'
	replace: true
	templateUrl: 	'views/error-view/error.html'

	scope:
		tapToRetry: '&'
		errorType: '='
		setTy: '='
	
	link: (scope, el, attr)->

		scope.errorMsg = scope.errorType

		scope.onTryAgain = ->
			scope.tapToRetry()

]

