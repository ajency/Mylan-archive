angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'Storage', 'QuestionAPI','$stateParams'
	, ($scope, App, Storage, QuestionAPI, $stateParams)->

		$scope.view =
			title: 'C-weight'
			data : []
			go : ''

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

			nextQuestion : ->
				options = 
					quizID: $stateParams.quizID
					questionId : @data.questionId
					answerId : @go
					action : 'submitted'

				QuestionAPI.saveAnswer options
				.then (data)=>
					@data = data 
					App.navigate 'questionnaire', quizID: '1111'
				, (error)=>
					console.log 'err'





			
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
