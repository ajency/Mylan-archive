angular.module 'angularApp.dashboard',[]

.controller 'dashboardController', ['$scope', 'DashboardAPI', '$location', ($scope, DashboardAPI, $location)->
	
	$scope.view =
		data : []
		init :() -> 
			console.log 'inside inita2323'
			console.log(refernceCode);
			id = refernceCode
			DashboardAPI.get(id)
			.then (data)=>
				@data = data.result
				console.log 'inside then'
				console.log @data
				@display = 'noError'
			,(error)=>
				@display = 'error'
				@errorType = error

		summary : (id)->
			$location.path('summary/'+id);
]

.controller 'EachRequestTimeCtrl', ['$scope', ($scope)->
	setTime = ->
		$scope.submissions.date =  moment($scope.submissions.occurrenceDate.iso).format('MMMM Do YYYY')
	setTime()
]
