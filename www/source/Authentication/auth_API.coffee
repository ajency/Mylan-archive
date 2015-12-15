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


		defer = $q.defer()

		url = AUTH_URL+'/user/dosetup'
				
		App.sendRequest(url, params, AUTH_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise

	AuthAPI.validateUser = (refrencecode,password )->

		defer = $q.defer()
		App.getInstallationId()
		.then (installationId)->
			console.log '--installtionId--'
			console.log installationId
			params = 
				"referenceCode" : refrencecode
				"password": password
				"installationId" : installationId
			url = AUTH_URL+'/user/login'
					
			App.sendRequest(url, params, AUTH_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise
			
	
	AuthAPI.setPassword = (refrencecode,password)->
		params = 
			"referenceCode" : refrencecode
			"password": password

		defer = $q.defer()

		url = AUTH_URL+'/user/setpassword'
				
		App.sendRequest(url, params, AUTH_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise

	AuthAPI
]