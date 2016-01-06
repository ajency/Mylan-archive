angular.module 'angularApp.dashboard'


.factory 'DashboardAPI', ['$q', '$http' ,($q, $http)->

	DashboardAPI = {}

	DashboardAPI.get = (id)->
		defer = $q.defer()

		PARSE_URL = 'https://api.parse.com/1/functions'

		url = PARSE_URL+'/dashboard'

		PARSE_HEADERS =
			headers:
				"X-Parse-Application-Id" : 'MQiH2NRh0G6dG51fLaVbM0i7TnxqX2R1pKs5DLPA'
				"X-Parse-REST-API-KeY" : 'I4yEHhjBd4e9x28MvmmEOiP7CzHCVXpJxHSu5Xva'

		param = 
			"patientId": id
				
		$http.post url,  param, PARSE_HEADERS
		.then (data)->
			console.log 'dashboard data '
			console.log data
			defer.resolve data.data
		, (error)=>
			defer.reject @errorCode(error)

		defer.promise


	DashboardAPI	
]