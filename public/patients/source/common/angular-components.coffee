angular.module 'angularApp.common'

.factory 'CToast', ['$q', '$http', ($q, $http)->

	CToast = {}

	CToast.show = (content)->
		console.log content

	CToast

]