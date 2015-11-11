angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'Storage', 'QuestionAPI','$stateParams', 
	'$window', ($scope, App, Storage, QuestionAPI, $stateParams, $window)->

		$scope.view =
			title: 'C-weight'
			data : []
			go : ''
			response : ''

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
					@response = data 
					console.log @response
					if @response.type == 'nextQuestion' 
						console.log 'next question'
						$window.location.reload()
						# App.navigate 'questionnaire', quizID: @response.quizID
					else
						console.log 'summary'
						App.navigate 'summary', quizID: @response.quizID
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
