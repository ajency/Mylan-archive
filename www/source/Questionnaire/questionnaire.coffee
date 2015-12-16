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
			descriptiveAnswer : ''

			variables :()->
				@descriptiveAnswer = ''
				@singleChoiceValue = ''
				@val_answerValue = {}


			getLocal :()->
				defer = $q.defer()
				Storage.getNextQuestion 'get'
				.then (details)->
					defer.resolve details
				defer.promise

			getQuestion :() ->
				# "patientId":refcode
				# "questionnaireId": patientData.questionnaire.id
				

				@display = 'loader'

				Storage.setData 'refcode','get'
				.then (refcode)=>
				
					Storage.setData 'patientData','get'
					.then (patientData)=>
						@patientId = patientData.patient_id

						@respStatus = $stateParams.respStatus
						if @respStatus == 'noValue'
							responseId = ''
						else 
							responseId = $stateParams.respStatus

						options =
							"responseId": responseId
							"questionnaireId": patientData.questionnaire.id
							"patientId": refcode

						QuestionAPI.getQuestion options
						.then (data)=>
							console.log 'inside then'
							console.log data
							@data = data.result
							Storage.setData 'responseId', 'set', data.result.responseId
							@display = 'noError'
						,(error)=>
							@display = 'error'
							@errorType = error
					
			init : ->
				@data = ''
				@getQuestion()	
					

			navigate : ->
				App.navigate 'summary', quizID: 111
			
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
						@variables()
						@data = []
						@data = data.result
						console.log '---loadNextQuestion---'
						console.log @data.hasAnswer
						if !_.isEmpty(@data.hasAnswer)
							console.log 'not emty hasAnswer'
							@hasAnswerShow()
						if !_.isUndefined(@data.status)
							summary = {}
							summary['summary'] = @data.summary
							summary['responseId'] = responseId
							Storage.summary('set', summary)
							App.navigate 'summary', summary:'set'
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
							"options": [optionId[0]]
							"value": valueInput[0]

						@loadNextQuestion(options)

			
				if @data.questionType == 'multi-choice'
					console.log '------multi-choice optionss -----'
					console.log @data.options

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

				if @data.questionType == 'descriptive'

					if (@descriptiveAnswer == '')
						CToast.show 'Please Fill in the following'
					else
						options =
							"questionId" : @data.questionId
							"options": []
							"value": @descriptiveAnswer

						@loadNextQuestion(options)


			prevQuestion : ->
				CSpinner.show '', 'Please wait..'
				Storage.setData 'responseId','get'
				.then (responseId)=>
					param =
						"responseId" : responseId
						"questionId" : @data.questionId
						"options": []
						"value": ""

					QuestionAPI.getPrevQuest param
					.then (data)=>
						console.log 'previous data'
						console.log data

						@variables()
						@data = []
						@data = data.result

						if @data.questionType == 'descriptive'
							@descriptiveAnswer = @data.hasAnswer.value

						if @data.questionType == 'single-choice'
							@singleChoiceValue = @data.hasAnswer.option[0]

						if @data.questionType == 'multi-choice'
							_.each @data.options, (value) =>
								if (_.contains(@data.hasAnswer.option, value.id))
									value['checked'] = true

						if @data.questionType == 'input'
							ObjId = _.findWhere(@data.options, {id: @data.hasAnswer.option[0]})
							console.log 'objjj id'
							console.log ObjId
							console.log 'valAnswer1'
							console.log @val_answerValue
							@val_answerValue[ObjId.option] = @data.hasAnswer.value
							console.log 'valAnswer2'
							console.log @val_answerValue
									
						console.log @data	
							



					,(error)=>
						console.log error
						if error == 'offline'
							CToast.showLongBottom 'Check net connection,answer not saved'
						else
							CToast.show 'Error ,try again'
					.finally ->
						CSpinner.hide()


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
				moment(date).format('MMMM Do YYYY')

			pastAnswer:(previousQuestionnaireAnswer, optionId )->
				# console.log 'passtAnswerr'
				# console.log previousQuestionnaireAnswer
				# console.log optionId
				optId = _.pluck(optionId, 'id')
				indexOf = optId.indexOf(previousQuestionnaireAnswer)
				indexOf++

				indexOf

			pastAnswerLabel:(optId)->
				if !_.isEmpty optId
					ObjId = _.findWhere(@data.options, {id: optId})
					ObjId.option


			hasAnswerShow:()->
				if @data.questionType == 'descriptive'
					@descriptiveAnswer = @data.hasAnswer.value

				if @data.questionType == 'single-choice'
					@singleChoiceValue = @data.hasAnswer.option[0]

				if @data.questionType == 'multi-choice'
					_.each @data.options, (value) =>
						if (_.contains(@data.hasAnswer.option, value.id))
							value['checked'] = true

				if @data.questionType == 'input'
					ObjId = _.findWhere(@data.options, {id: @data.hasAnswer.option[0]})
					console.log 'objjj id'
					console.log ObjId
					console.log 'valAnswer1'
					console.log @val_answerValue
					@val_answerValue[ObjId.option] = @data.hasAnswer.value
					console.log 'valAnswer2'
					console.log @val_answerValue




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
			url: '/questionnaire:respStatus'
			parent: 'parent-questionnaire'
			cache: false
			views: 
				"QuestionContent":
					templateUrl: 'views/questionnaire/question.html'
					controller: 'questionnaireCtr'
]
