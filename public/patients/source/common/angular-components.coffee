angular.module 'angularApp.common'

.factory 'CToast', ['$q', '$http', ($q, $http)->

	CToast = {}

	CToast.show = (content)->
		$("#notify-css").notify(content, {position:"right"});

	CToast

]