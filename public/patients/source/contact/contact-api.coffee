angular.module 'angularApp.contact'

.factory 'contactAPI', ['$q', '$http', 'App', ($q, $http, App, $stateParams)->

	contactAPI = {}

	contactAPI.sendEmail = (param)->

		defer = $q.defer()

		url = AUTH_URL+'/user/contactus'
				
		App.sendRequest(url, param, AUTH_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise
		

	

	
	contactAPI

]