angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'$window', 'Storage', 'CToast', 'CSpinner'
	($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner)->

		$scope.view =
			# noError / error / loader
			pastAnswerDiv : 0
			title: 'C-weight'
			data : []
			go : ''
			response : ''
			actionValue : {}
			errorType : 'No network connection'
			display : 'noError'
			
			
			getQuestion :(questNo) ->
				options = 
					quizID: $stateParams.quizID
					questNo: questNo

				QuestionAPI.getQuestion options
				.then (data)=>
					Storage.getNextQuestion('get').then (value) =>
						value = parseInt(value)
						if value == 1
							data.questionType = 'mcq'
						else if value == 2
							data.questionType = 'scq'
							data.questionTittle = 'Has Your weight changed in the past month ?'
							data.option =
								0:
								 id : '1'
								 answer : 'No change'
								 value : 'no_pain'
								 checked: false
								1:
								 id : '2'
								 answer : 'Lost upto 4 pounds'
								 value : 'pain_present'
								 checked: false
						else
							data.questionType = 'descr'

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
				@data = ''
				Storage.getNextQuestion('get').then (value) ->
				@getQuestion()	
					


				# console.log 'init'
				# @actionValue = QuestionAPI.setAction 'get'
				# console.log @actionValue
				# if _.isEmpty(@actionValue) || @actionValue.mode == 'next'
				# 	@getQuestion()
				# else
				# 	@getPrevQuestion()


			navigate : ->
				Storage.getNextQuestion 'set' , value
				Storage.getNextQuestion('get').then (value) ->
					value = parseInt(value)
					value++
					if value == 4
						App.navigate 'summary', quizID: 111
					else

						Storage.getNextQuestion 'set' , value
						$window.location.reload()


			nextQuestion : ->
				# CSpinner.show '', 'Please wait..'
				# CSpinner.hide()
				# CSpinner.show '', 'Please wait...'
				if @data.questionType == 'descr'
					error = 0
					sizeOfField = _.size(@data.fields)
					sizeOfTestboxAns = _.size(@val_answerValue)
					console.log '******----******'
					console.log sizeOfTestboxAns
					if (sizeOfTestboxAns == 0)
						error = 1
					else
						_.each @val_answerValue, (value)->
							if value == null
								error = 1

					if error == 1
						CToast.show 'Please enter the values'
					else
						@navigate()
						

				else if @data.questionType == 'scq'
					if @go == ''
				 		CToast.show 'Please select your answer'
				 	else 
				 		@navigate()
				 		

				else if @data.questionType == 'mcq'
					if ! _.contains(_.pluck(@data.option, 'checked'), true)
						CToast.show 'Please select your answer'
					else
						@navigate()
						





				

				# if data.questionType == scq
				# if @go == ''
				# 	ctoast('please select value')

				# if data.questionType == mcq
				# if ! _.contains(_.pluck(@data.option, 'checked'), 'true')
				# 	ctoast('please select value')

				# if @go == ''
				# 	ctoast('please select value')


				# console.log 'nextt questt'
				# console.log @go
				# options = 
				# 	quizID: $stateParams.quizID
				# 	questionId : @data.questionId
				# 	answerId : @go
				# 	action : 'submitted'

				# QuestionAPI.saveAnswer options
				# .then (data)=>
				# 	action =
				# 		questionId : @data.questionId
				# 		mode : 'next'

				# 	QuestionAPI.setAction 'set', action

				# 	v = QuestionAPI.setAction 'get'
				# 	console.log v

				# 	@response = data 
				# 	if @response.type == 'nextQuestion' 
				# 		$window.location.reload()
				# 		# App.navigate 'questionnaire', quizID: @response.quizID
				# 	else
				# 		App.navigate 'summary', quizID: @response.quizID
				# , (error)=>
				# 	console.log 'err'

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
				@data = []
				@pastAnswerDiv = 0
				@go = ''

			onTapToRetry : ->
				console.log 'onTapToRetry'

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reInit()

		
]


.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'questionnaire',
			url: '/questionnaire:quizID'
			parent: 'parent-questionnaire'
			cache: false
			views: 
				"QuestionContent":
					templateUrl: 'views/questionnaire/question.html'
					controller: 'questionnaireCtr'
]
