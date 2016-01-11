angular.module 'angularApp.dashboard',[]

.controller 'dashboardController', ['$scope', 'DashboardAPI', '$location', ($scope, DashboardAPI, $location)->
	
	$scope.view =
		data : []
		display : 'loader'

		init :() -> 
			@display = 'loader'
			console.log 'inside inita2323'
			console.log(RefCode);
			id = RefCode
			param = 
				"patientId": id

			DashboardAPI.get(param)
			.then (data)=>
				@data = data
				console.log 'inside then'
				console.log @data
				@display = 'noError'
			,(error)=>
				@display = 'error'
				@errorType = error

		summary : (id)->
			$location.path('summary/'+id)

		startQuiz :() ->
			$location.path 'start-questionnaire'

		resumeQuiz : (id)->

			console.log 'resumeQuiz'
			console.log id
			$location.path 'questionnaire/'+id+'/000'
			
		onTapToRetry : ->
				@display = 'loader'
				console.log 'onTapToRetry'
				@init()

				
]

.controller 'EachRequestTimeCtrl', ['$scope', ($scope)->
	setTime = ->
		# moment($scope.submissions.occurrenceDate.iso).format('MMMM Do YYYY')
		console.log moment($scope.submissions.occurrenceDate).format('Do')
		$scope.submissions.yr =  moment($scope.submissions.occurrenceDate.iso).format('YYYY')
		$scope.submissions.month =  moment($scope.submissions.occurrenceDate.iso).format('MMM')
		$scope.submissions.date =  moment($scope.submissions.occurrenceDate.iso).format('Do')
	setTime()
]
