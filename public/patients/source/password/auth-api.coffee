angular.module 'angularApp.Auth'

.factory 'AuthAPI', ['$q', 'App', '$http', ($q, App, $http)->
	AuthAPI = {}
	
	
	AuthAPI.setPassword = (refrencecode,password)->
		params = 
			"referenceCode" : refrencecode
			"password": password

		defer = $q.defer()

		url = Url+'/api/v1/user/setpassword'

		AUTH_HEADERS = 
			headers:
				"X-API-KEY" : APP_KEY 
				"X-Authorization" : APP_AuthrizationKey
				"Content-Type" : 'application/json'
				
		App.sendRequest(url, params, AUTH_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise

	AuthAPI
]