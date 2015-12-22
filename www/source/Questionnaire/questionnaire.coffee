angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout', '$ionicPlatform',
	($scope, App, QuestionAPI, $stateParams, $window, Storage,
	 CToast, CSpinner, $q, $timeout, $ionicPlatform)->

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
			flag : true
			readonly : true

			variables :()->
				@descriptiveAnswer = ''
				@singleChoiceValue = ''
				@val_answerValue = {}

			getQuestion :() ->

				# "responseId": responseId
				# "questionnaireId": patientData.id
				# "patientId": refcode

				@display = 'loader'
				Storage.setData 'refcode','get'
				.then (refcode)=>
					@refcode = refcode
					Storage.setData 'patientData','get'
				.then (patientData)=>
					@respStatus = $stateParams.respStatus
					if @respStatus == 'noValue'
						responseId = ''
					else 
						responseId = $stateParams.respStatus

					options =
						"responseId": responseId
						"questionnaireId": patientData.id
						"patientId": @refcode

					QuestionAPI.getQuestion options
					.then (data)=>
						console.log 'inside then'
						console.log data
						@data = data.result
						@pastAnswer()
						Storage.setData 'responseId', 'set', data.result.responseId
						@display = 'noError'
					,(error)=>
						@display = 'error'
						@errorType = error
					
			init : ->
				@getQuestion()	
					
			loadNextQuestion :(param)->
				Storage.setData 'responseId','get'
				.then (responseId)=>	
					CSpinner.show '', 'Please wait..'
					param.responseId = responseId
					QuestionAPI.saveAnswer param
					.then (data)=>
						App.resize()
						CToast.show 'Your answer is saved'
						console.log '******next question******'
						console.log data
						@variables()
						@data = []
						@data = data.result
						@readonly = true

						if !_.isEmpty(@data.hasAnswer)
							@hasAnswerShow()
							@readonly = @data.editable



						@pastAnswer()

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

						_.each @data.options, (opt)=>
							a = @val_answerValue[opt.option]
							if !_.isUndefined(a) && a !=''
								valueInput.push(a)
								optionId.push(opt.id)

						options =
							"questionId" : @data.questionId
							"options": [optionId[0]]
							"value": valueInput[0].toString()

						@loadNextQuestion(options)

			
				if @data.questionType == 'multi-choice'
					if ! _.contains(_.pluck(@data.options, 'checked'), true)
						CToast.show 'Please select your answer'
					else
						selectedvalue = []

						_.each @data.options, (opt)->
							if opt.checked == true
								selectedvalue.push opt.id		

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


			loadPrevQuestion :(param)->
				Storage.setData 'responseId','get'
				.then (responseId)=>	
					CSpinner.show '', 'Please wait..'
					param.responseId = responseId
					QuestionAPI.getPrevQuest param
					.then (data)=>
						console.log 'previous data'
						console.log @data	
						@variables()
						@data = []
						@data = data.result
						@readonly = @data.editable
						@pastAnswer()
						if !_.isEmpty(@data.hasAnswer)
							@hasAnswerShow()	
						console.log @data	
					,(error)=>
						console.log error
						if error == 'offline'
							CToast.showLongBottom 'Check net connection,answer not saved'
						else
							CToast.show 'Error ,try again'
					.finally ->
						CSpinner.hide()


			prevQuestion : ->
				if @data.questionType == 'single-choice'
					options =
						"questionId" : @data.questionId
						"options": [@singleChoiceValue]
						"value": ""

					@loadPrevQuestion(options)

				if @data.questionType == 'multi-choice'
					
					selectedvalue = []

					_.each @data.options, (opt)->
						if opt.checked == true
							selectedvalue.push opt.id		

					options =
						"questionId" : @data.questionId
						"options": selectedvalue
						"value": ""

					@loadPrevQuestion(options)

				if @data.questionType == 'descriptive'
					options =
							"questionId" : @data.questionId
							"options": []
							"value": @descriptiveAnswer

					@loadPrevQuestion(options)

				if @data.questionType == 'input'

					valueInput = []
					optionId = []

					_.each @data.options, (opt)=>
						a = @val_answerValue[opt.option]
						if !_.isUndefined(a) && a !=''
							valueInput.push(a)
							optionId.push(opt.id)

					options =
						"questionId" : @data.questionId
						"options": [optionId[0]]
						"value": valueInput[0].toString()

					@loadPrevQuestion(options)




				# CSpinner.show '', 'Please wait..'
				# Storage.setData 'responseId','get'
				# .then (responseId)=>
				# 	param =
				# 		"responseId" : responseId
				# 		"questionId" : @data.questionId
				# 		"options": []
				# 		"value": ""

				# 	QuestionAPI.getPrevQuest param
				# 	.then (data)=>
				# 		console.log 'previous data'
				# 		console.log @data	
				# 		@variables()
				# 		@data = []
				# 		@data = data.result
				# 		@readonly = @data.editable
				# 		@pastAnswer()
				# 		if !_.isEmpty(@data.hasAnswer)
				# 			@hasAnswerShow()	
				# 		console.log @data	
						
				# 	,(error)=>
				# 		console.log error
				# 		if error == 'offline'
				# 			CToast.showLongBottom 'Check net connection,answer not saved'
				# 		else
				# 			CToast.show 'Error ,try again'
				# 	.finally ->
				# 		CSpinner.hide()


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

			pastAnswer:()->
				previousAns = @data.previousQuestionnaireAnswer

				if !_.isEmpty(previousAns)

					if @data.questionType == 'input'
						if !_.isEmpty previousAns.optionId[0] 
							ObjId = _.findWhere(@data.options, {id: previousAns.optionId[0]})
							ObjId.option
							@data.previousQuestionnaireAnswer['label'] = ObjId.option

					if @data.questionType == 'single-choice' || @data.questionType == 'multi-choice'
						console.log 'have an'
						optionSelectedArray = []
						sortedArray = _.sortBy( @data.options, 'score' )
						pluckId = _.pluck(sortedArray, 'id')
						_.each previousAns.optionId, (value) =>
							a = _.indexOf(pluckId, value)
							if a != -1
								a++
								optionSelectedArray.push(a)
						@data.previousQuestionnaireAnswer['label'] = optionSelectedArray.toString()

					@data.previousQuestionnaireAnswer.date = moment(previousAns.date.iso).format('MMMM Do YYYY')

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
					@val_answerValue[ObjId.option] = parseInt(@data.hasAnswer.value)

		onDeviceBack = ->
			if $scope.view.data.previous == false || _.isElement($scope.view.data) 
				App.navigate 'dashboard', {}, {animate: false, back: false}
			else
				$scope.view.prevQuestion()

					

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reInit()

		$scope.$on '$ionicView.enter', ->
			#Device hardware back button for android
			$ionicPlatform.onHardwareBackButton onDeviceBack
		

		$scope.$on '$ionicView.leave', ->
			$ionicPlatform.offHardwareBackButton onDeviceBack

		
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
