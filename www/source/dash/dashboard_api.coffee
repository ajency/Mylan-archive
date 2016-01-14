angular.module 'PatientApp.dashboard'

.factory 'DashboardAPI', ['$q', '$http', 'App', '$stateParams', ($q, $http, App, $stateParams)->

	DashboardAPI = {}

	DashboardAPI.get = (param)->
		
		defer = $q.defer()		
		App.SendParseRequest('dashboard', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise


	DashboardAPI	
]