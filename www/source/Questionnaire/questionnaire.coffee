angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout'
	($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner, $q, $timeout)->

		$scope.view =
			# noError / error / loader
			pastAnswerDiv : 0
			title: 'C-weight'
			data : []
			singleChoiceValue : ''
			response : ''
			actionValue : {}
			errorType : ''
			display : 'loader'
			infoBox : true

			
			getLocal :()->
				defer = $q.defer()
				Storage.getNextQuestion 'get'
				.then (details)->
					defer.resolve details
				defer.promise

			getQuestion :(questNo) ->
				@display = 'loader'

				Storage.setData 'refcode','get'
				.then (refcode)=>
				
					Storage.setData 'patientData','get'
					.then (patientData)=>
						@patientId = patientData.patient_id
						options =
							"responseId": ''
							"questionnaireId": patientData.questionnaire.id
							"patientId":refcode

						QuestionAPI.getQuestion options
						.then (data)=>
							console.log 'inside then'
							console.log data
							@data = data.result
							Storage.setData 'responseId', 'set', data.result.responseId
							@display = 'noError'

							# $timeout =>
							# 	console.log 'timeoutt'
							# 	@infoBox = false
							# , 30000
						,(error)=>
							@display = 'error'
							@errorType = error


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
				@getQuestion()	
					

			navigate : ->
				App.navigate 'summary', quizID: 111
				# value = 
				# @getLocal()
				# .then (result)=> 
				# 	value = result
				# 	# Storage.getNextQuestion('get').then (value) ->
				# 	value = parseInt(value)
				# 	value++
				# 	Storage.getNextQuestion 'set' , value
				# 	if value == 4
				# 		CSpinner.hide()
				# 		App.navigate 'summary', quizID: 111
				# 	else

				# 		Storage.getNextQuestion 'set' , value
				# 		# $window.location.reload()

				# 		$timeout ->
				# 			CSpinner.hide()
				# 			$window.location.reload()
				# 		,500

			loadNextQuestion :(param)->
				Storage.setData 'responseId','get'
				.then (responseId)=>	
					CSpinner.show '', 'Please wait..'

					param.responseId = responseId

					QuestionAPI.saveAnswer param
					.then (data)=>
						App.resize()
						CToast.show 'Your answer is saved'
						console.log '******'
						console.log 'next question'
						console.log data
						@singleChoiceValue = ''

						@val_answerValue = ''
						@data = []
						@data = data.result
						@display = 'noError'					
					,(error)=>
						console.log 'inside save error'
						console.log error
						if error == 'offline'
							CToast.showLongBottom 'Check net connection,answer not saved'
						else
							CToast.show 'Error in saving answer,try again'

					.finally ->
						CSpinner.hide()

			nextQuestion : ->
		
				if @data.questionType == 'single-choice'

					if @singleChoiceValue == ''
						CToast.show 'Please select atleast one answer'
					else
						options =
							"questionId" : @data.questionId
							"options": [@singleChoiceValue]
							"value": ""

						@loadNextQuestion(options)

				if @data.questionType == 'input'

					error = 0
					sizeOfField = _.size(@data.options)
					sizeOfTestboxAns = _.size(@val_answerValue)

					if (sizeOfTestboxAns == 0)
						error = 1
					else
						_.each @val_answerValue, (value)->
							if value == null
								error = 1

					if error == 1
						CToast.show 'Please enter the values'
					else
						valueInput = []
						optionId = []

						console.log 'uuuu0'
						console.log @val_answerValue
						console.log 'uuuu0'
						console.log @data.options

						_.each @data.options, (opt)=>
							a = @val_answerValue[opt.option]
							if !_.isUndefined(a) && a !=''
								valueInput.push(a)
								optionId.push(opt.id)

						options =
							"questionId" : @data.questionId
							"options": optionId
							"value": valueInput

						@loadNextQuestion(options)

			
				if @data.questionType == 'multi-choice'

					if ! _.contains(_.pluck(@data.options, 'checked'), true)
						CToast.show 'Please select your answer'
					else
						selectedvalue = []

						_.each @data.options, (opt)->
							if opt.checked == true
								selectedvalue.push opt.id		

					console.log 'selectedvalue'
					console.log selectedvalue

					options =
						"questionId" : @data.questionId
						"options": selectedvalue
						"value": ""

					@loadNextQuestion(options)

				# CSpinner.show '', 'Please wait..'
				# # CSpinner.hide()
				# # CSpinner.show '', 'Please wait...'
				# if @data.questionType == 'descr'
				# 	error = 0
				# 	sizeOfField = _.size(@data.fields)
				# 	sizeOfTestboxAns = _.size(@val_answerValue)
				# 	console.log '******----******'
				# 	console.log sizeOfTestboxAns
				# 	if (sizeOfTestboxAns == 0)
				# 		error = 1
				# 	else
				# 		_.each @val_answerValue, (value)->
				# 			if value == null
				# 				error = 1

				# 	if error == 1
				# 		CToast.show 'Please enter the values'
				# 	else
				# 		@navigate()
						

				# else if @data.questionType == 'scq'
				# 	if @go == ''
				#  		CToast.show 'Please select your answer'
				#  	else 
				#  		@navigate()
				 		

				# else if @data.questionType == 'mcq'
				# 	if ! _.contains(_.pluck(@data.option, 'checked'), true)
				# 		CToast.show 'Please select your answer'
				# 	else
				# 		@navigate()
						

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
				@display = 'loader'

			onTapToRetry : ->
				@display = 'loader'
				console.log 'onTapToRetry'
				@getQuestion()

			isEmpty :(pastAnswerObject)->
				_.isEmpty(pastAnswerObject)

			pastDate:(date)->
				console.log 'sdsdsdsd'
				moment(date).format('MMMM Do YYYY')

			pastAnswer:(previousQuestionnaireAnswer, optionId )->
				optId = _.pluck(optionId, 'id')
				console.log optId
				indexOf = optId.indexOf(previousQuestionnaireAnswer)
				indexOf++

				indexOf


		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reInit()

		$scope.$on '$ionicView.afterEnter', (event, viewData)->
			# $timeout ->
			# 	console.log 'timeoutt'
			# 	$scope.view.infoBox = false
			# , 300

		
]

.controller 'PastAnswerCtrl', ['$scope', ($scope )->

	console.log 'Request time'
	console.log $scope.view.data.previousQuestionnaireAnswer
	optId = _.pluck($scope.view.data.options, 'id')
	console.log optId
	indexOf = optId.indexOf($scope.view.data.previousQuestionnaireAnswer.optionId[0])
	indexOf++

	$scope.view.data.lastOption = indexOf
	date = $scope.view.data.previousQuestionnaireAnswer.date.iso
	$scope.view.data.submitedDate = moment(date).format('MMMM Do YYYY')
	
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
