angular.module 'angularApp.dashboard'


.factory 'DashboardAPI', ['$q', '$http', 'App' ,($q, $http , App)->

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