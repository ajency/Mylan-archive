angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'Storage', 'QuestionAPI','$stateParams'
	, ($scope, App, Storage, QuestionAPI, $stateParams)->

		$scope.view =
			title: 'C-weight'
			data : []

			getQuestion : ->
				options = 
					quizID: $stateParams.quizID

				QuestionAPI.getQuestion options
				.then (data)=>
					@data = data 
				, (error)=>
					console.log 'err'
					
			init : ->
				@getQuestion()
			
]


.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'questionnaire',
			url: '/questionnaire:quizID'
			parent: 'parent-questionnaire'
			views: 
				"QuestionContent":
					templateUrl: 'views/questionnaire/question.html'
					controller: 'questionnaireCtr'
]
