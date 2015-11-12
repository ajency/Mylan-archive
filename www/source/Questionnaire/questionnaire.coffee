angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'$window', ($scope, App, QuestionAPI, $stateParams, $window)->

		$scope.view =
			title: 'C-weight'
			data : []
			go : ''
			response : ''
			actionValue : {}

			getQuestion : ->
				options = 
					quizID: $stateParams.quizID

				QuestionAPI.getQuestion options
				.then (data)=>
					@data = data 
				, (error)=>
					console.log 'err'

			getPrevQuestion : ->
				options = 
					quizID: $stateParams.quizID
					questionId : @actionValue.questionId

				QuestionAPI.getQuestion options
				.then (data)=>
					@data = data 
				, (error)=>
					console.log 'err'

					
			init : ->
				console.log 'init'
				@actionValue = QuestionAPI.setAction 'get'
				console.log @actionValue
				if _.isEmpty(@actionValue) || @actionValue.mode == 'next'
					@getQuestion()
				else
					@getPrevQuestion()

				

			nextQuestion : ->
				options = 
					quizID: $stateParams.quizID
					questionId : @data.questionId
					answerId : @go
					action : 'submitted'

				QuestionAPI.saveAnswer options
				.then (data)=>
					action =
						questionId : @data.questionId
						mode : 'next'

					QuestionAPI.setAction 'set', action

					v = QuestionAPI.setAction 'get'
					console.log v

					@response = data 
					if @response.type == 'nextQuestion' 
						$window.location.reload()
						# App.navigate 'questionnaire', quizID: @response.quizID
					else
						App.navigate 'summary', quizID: @response.quizID
				, (error)=>
					console.log 'err'

			prevQuestion : ->
				action =
					questionId : @data.questionId
					mode : 'prev'

				QuestionAPI.setAction 'set', action

				@init()

		
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
