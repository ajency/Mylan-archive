angular.module 'angularApp.dashboard',[]

.controller 'dashboardController', ['$scope', 'DashboardAPI', '$location', ($scope, DashboardAPI, $location)->
	
	$scope.view =
		data : []
		display : 'loader'

		init :() -> 
			@display = 'loader'
			id = RefCode
			param = 
				"patientId": id

			DashboardAPI.get(param)
			.then (data)=>
				@data = data
				@display = 'noError'
			,(error)=>
				@display = 'error'
				@errorType = error

		summary : (id)->
			$location.path('summary/'+id)

		startQuiz :() ->
			$location.path 'start-questionnaire'

		resumeQuiz : (id)->
			$location.path 'questionnaire/'+id+'/000'

		onTapToRetry : ->
				@display = 'loader'
				console.log 'onTapToRetry'
				@init()

				
]

.controller 'EachRequestTimeCtrl', ['$scope', ($scope)->
	setTime = ->
		$scope.submissions.yr =  moment($scope.submissions.occurrenceDate).format('YYYY')
		$scope.submissions.month =  moment($scope.submissions.occurrenceDate).format('MMM')
		$scope.submissions.date =  moment($scope.submissions.occurrenceDate).format('Do')
	setTime()
]
