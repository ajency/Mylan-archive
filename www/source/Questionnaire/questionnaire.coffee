angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'$window', 'Storage', ($scope, App, QuestionAPI, $stateParams, $window, Storage)->

		$scope.view =
			pastAnswerDiv : 0
			title: 'C-weight'
			data : []
			go : 'no_pain'
			response : ''
			actionValue : {}



			getQuestion : ->
				Storage.login('get').then (value) ->
					console.log '*****************'
					console.log value
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
				console.log @data.option

				# if data.questionType == scq
				# if @go == ''
				# 	ctoast('please select value')

				# if data.questionType == mcq
				# if ! _.contains(_.pluck(@data.option, 'checked'), 'true')
				# 	ctoast('please select value')

				# if @go == ''
				# 	ctoast('please select value')


				console.log 'nextt questt'
				console.log @go
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

			showDiv : ->
				@pastAnswerDiv = 1

			hideDiv : ->
				@pastAnswerDiv = 0

			reInit : ->
				@pastAnswerDiv = 0

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reInit()

		
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
