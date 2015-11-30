angular.module 'PatientApp.Auth'

.factory 'AuthAPI', ['$q', 'App', '$http','UrlList', ($q, App, $http,UrlList)->
	AuthAPI = {}
	
	AuthAPI.validateRefCode = (refcode, deviceUUID ,deviceOS)->
		params = 
			"referenceCode" : refcode
			"deviceType": 'mobile'
			"deviceIdentifier" : deviceUUID
			"deviceOS" : deviceOS
			"accessType" : 'app'

		headers =
			headers:
				"X-API-KEY" : 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y'
				"X-Authorization" : 'e7544bd1e3743b71ea473cee30d73227135358aa'
				"Content-Type" : 'application/json'


		defer = $q.defer()

		$http.post AUTH_URL+'/user/dosetup',  params, headers
		.then (data)->
			console.log 'succ'
			console.log data
			defer.resolve data.data
		, (error)->
			console.log 'eroor'
			defer.reject error

		defer.promise	

	AuthAPI.validateUser = (refrencecode,password )->
		# console.log refcode + password
		params = 
			"referenceCode" : refrencecode
			"password": password
			

		headers =
			headers:
				"X-API-KEY" : 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y'
				"X-Authorization" : 'e7544bd1e3743b71ea473cee30d73227135358aa'
				"Content-Type" : 'application/json'


		defer = $q.defer()

		$http.post AUTH_URL+'/user/login',  params, headers
		.then (data)->
			defer.resolve data.data
		, (error)->
			defer.reject error

		defer.promise				
	
	AuthAPI.setPassword = (refrencecode,password)->
		params = 
			"referenceCode" : refrencecode
			"password": password
			
		headers =
			headers:
				"X-API-KEY" : 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y'
				"X-Authorization" : 'e7544bd1e3743b71ea473cee30d73227135358aa'
				"Content-Type" : 'application/json'

		defer = $q.defer()

		$http.post AUTH_URL+'/user/setpassword',  params, headers
		.then (data)->
			defer.resolve data.data
		, (error)->
			defer.reject error

		defer.promise


	AuthAPI
]