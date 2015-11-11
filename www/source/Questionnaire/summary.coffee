angular.module 'PatientApp.Quest'

.controller 'SummaryCtr',['$scope', 'App', 'Storage', 'QuestionAPI','$stateParams', 
	'$window', ($scope, App, Storage, QuestionAPI, $stateParams, $window)->

		$scope.view =
			title: 'C-weight'
			data : []
			go : ''
			response : ''

			getSummary : ->
				options = 
					quizID: $stateParams.quizID

				QuestionAPI.getSummary options
				.then (data)=>
					console.log data
					@data = data 
				, (error)=>
					console.log 'err'
					
			init : ->
				@getSummary()

			submitSummary : ->
				options = 
					quizID: $stateParams.quizID

				QuestionAPI.submitSummary options
				.then (data)=>
					App.navigate 'dashboard'
				, (error)=>
					console.log 'err'
		
]


.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'summary',
			url: '/summary:quizID'
			parent: 'parent-questionnaire'
			views: 
				"QuestionContent":
					templateUrl: 'views/questionnaire/summary.html'
					controller: 'SummaryCtr'
]
