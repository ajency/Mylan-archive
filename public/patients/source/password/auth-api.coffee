angular.module 'angularApp.Auth'

.factory 'AuthAPI', ['$q', 'App', '$http', ($q, App, $http)->
	AuthAPI = {}
	
	
	AuthAPI.setPassword = (refrencecode,password)->
		params = 
			"referenceCode" : refrencecode
			"password": password

		defer = $q.defer()

		url = 'http://mylantest.ajency.in/api/v1/user/setpassword'

		AUTH_HEADERS = 
			headers:
				"X-API-KEY" : 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y'
				"X-Authorization" : 'e7968bf3f5228312f344339f3f9eb19701fb7a3c'
				"Content-Type" : 'application/json'
				
		App.sendRequest(url, params, AUTH_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise

	AuthAPI
]