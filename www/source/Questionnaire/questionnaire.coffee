angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout', '$ionicPlatform','$ionicPopup',
	($scope, App, QuestionAPI, $stateParams, $window, Storage,
	 CToast, CSpinner, $q, $timeout, $ionicPlatform, $ionicPopup)->

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
			alertPopup : ''

			variables :()->
				@descriptiveAnswer = ''
				@singleChoiceValue = ''
				@val_answerValue = {}

			getQuestion :() ->
				@display = 'loader'
				Storage.setData 'refcode','get'
				.then (refcode)=>
					@refcode = refcode
					Storage.setData 'patientData','get'
				.then (patientData)=>
					@respStatus = $stateParams.respStatus
					if @respStatus == 'lastQuestion'
						param =
							"questionId" : ''
							"options": []
							"value": ""
						Storage.setData 'responseId','get'
						.then (responseId)=>	
							param.responseId = responseId
							QuestionAPI.getPrevQuest param
							.then (data)=>
								console.log 'previous data'
								console.log @data	
								@variables()
								@data = []
								@data = data
								@readonly = @data.editable
								@pastAnswer()
								if !_.isEmpty(@data.hasAnswer)
									@hasAnswerShow()	
								@display = 'noError'
							,(error)=>
								@display = 'error'
								@errorType = error

					else if @respStatus == 'noValue'
						responseId = ''

						options =
							"responseId": responseId
							"questionnaireId": patientData.id
							"patientId": @refcode

						QuestionAPI.getQuestion options
						.then (data)=>
							console.log 'inside then'
							console.log data
							@data = data
							@pastAnswer()
							Storage.setData 'responseId', 'set', data.responseId
							@display = 'noError'
						,(error)=>
							@display = 'error'
							@errorType = error

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
							@data = data
							if !_.isUndefined(@data.status)
								if @data.status == 'saved_successfully'
									CToast.show 'This questionnaire was already answer'
									App.navigate 'summary', summary:responseId
								else if @data.status == 'completed'
									CToast.show 'This questionnaire is completed '
								else if @data.status == 'missed'
									CToast.show 'This questionnaire was Missed'

							
							@pastAnswer()
							Storage.setData 'responseId', 'set', data.responseId
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
						if @readonly == true then CToast.show 'Your answer is saved'
						console.log '******next question******'
						console.log data

						if !_.isUndefined(data.status)
							if data.status == 'saved_successfully'
								App.navigate 'summary', summary:responseId
							else if data.status == 'completed'
								@title = 'This questionnaire was Completed'
								@showConfirm()
							else if data.status == 'missed'
								@title = 'This questionnaire was Missed'
								@showConfirm()

						@variables()
						@data = []
						@data = data
						@readonly = true

						if !_.isEmpty(@data.hasAnswer)
							@hasAnswerShow()
							@readonly = @data.editable
						@pastAnswer()

		

						@display = 'noError'					
					,(error)=>
						if error == 'offline'
							CToast.showLongBottom 'Check net connection,answer not saved'
						else if error == 'server_error'
							CToast.show 'Error in saving answer,Server error'
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
							console.log '************'
							console.log opt
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
						console.log data
						if !_.isUndefined(data.status)

							if data.status == 'completed'
								@title = 'This questionnaire was Completed'
								@showConfirm()
							else if data.status == 'missed'
								@title = 'This questionnaire was Missed'
								@showConfirm()

						@variables()
						@data = []
						@data = data
						

						@readonly = @data.editable
						@pastAnswer()
						if !_.isEmpty(@data.hasAnswer)
							@hasAnswerShow()	
					,(error)=>
						if error == 'offline'
							CToast.show 'Check net connection'
						else if error == 'server_error'
							CToast.showLongBottom 'Error in dispalying previous,Server error'
						else
							CToast.showLongBottom 'Error in dispalying previous,try again'
					.finally ->
						CSpinner.hide()


			prevQuestion : ->
				if @data.questionType == 'single-choice'
					
					options =
						"questionId" : @data.questionId
						"options": if @singleChoiceValue == '' then [] else [@singleChoiceValue]
						"value": ""

					@loadPrevQuestion(options)

				if @data.questionType == 'multi-choice'
					
					selectedvalue = []

					_.each @data.options, (opt)->
						if opt.checked == true
							selectedvalue.push opt.id		

					options =
						"questionId" : @data.questionId
						"options": if selectedvalue == [] then [] else selectedvalue
						"value": ""

					@loadPrevQuestion(options)

				if @data.questionType == 'descriptive'
					options =
							"questionId" : @data.questionId
							"options": []
							"value": if @descriptiveAnswer == '' then '' else @descriptiveAnswer

					@loadPrevQuestion(options)

				if @data.questionType == 'input'

					valueInput = []
					optionId = []

					_.each @data.options, (opt)=>
						a = @val_answerValue[opt.option]
						if !_.isUndefined(a) and !_.isEmpty(a)  and !_.isNull(a)
							valueInput.push(a)
							optionId.push(opt.id)

					console.log '***'
					console.log optionId

					# aa = if _.isEmpty(optionId) then [] else [optionId[0]] 
					# bb = if  _.isEmpty(valueInput) then [] else value = valueInput[0]	
					if  _.isEmpty(optionId)
						optionId = []
					else
					 	optionId = [optionId[0]] 	
					if  _.isEmpty(valueInput)
						value = []
					else
					 	value = valueInput[0].toString()
					options =
						"questionId" : @data.questionId
						"options": optionId
						"value": value.toString()

					@loadPrevQuestion(options)

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
						optionSelectedArray = []
						sortedArray = _.sortBy( @data.options, 'score' )
						pluckId = _.pluck(sortedArray, 'id')
						_.each previousAns.optionId, (value) =>
							a = _.indexOf(pluckId, value)
							if a != -1
								a++
								optionSelectedArray.push(a)
						@data.previousQuestionnaireAnswer['label'] = optionSelectedArray.toString()

					@data.previousQuestionnaireAnswer.dateDisplay = moment(previousAns.date).format('MMMM Do YYYY')

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

			navigateOnDevice:()->
				if $('.popup-container').hasClass('active')
					@alertPopup.close()
					App.navigate 'dashboard', {}, {animate: false, back: false}
				else	
					if @data.previous == false 
						# onHardwareBackButton1()
						App.navigate 'dashboard', {}, {animate: false, back: false}
					else
						$scope.view.prevQuestion()

			showConfirm : ->
			  	@alertPopup = $ionicPopup.alert(
			    	title: 'Alert'
			    	template: @title)

			  	@alertPopup.then (res) ->
				    if res
				      App.navigate 'dashboard', {}, {animate: false, back: false}
				    


		onDeviceBack = ->
			$scope.view.navigateOnDevice() 
			

					
		onHardwareBackButton1 = null 
			
		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reInit()
			


		$scope.$on '$ionicView.enter', ->
			console.log '$ionicView.enter questionarie'
			#Device hardware back button for android
			# $ionicPlatform.onHardwareBackButton onDeviceBack
			onHardwareBackButton1 = $ionicPlatform.registerBackButtonAction onDeviceBack, 1000
		

		$scope.$on '$ionicView.leave', ->
			console.log '$ionicView.leave'
			# onHardwareBackButton1()
			# console.log onHardwareBackButton1
			if onHardwareBackButton1 then onHardwareBackButton1()
			# $ionicPlatform.offHardwareBackButton onDeviceBack

]



.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'questionnaire',
			url: '/questionnaire:respStatus'
			parent: 'main'
			cache: false
			views: 
				"appContent":
					templateUrl: 'views/questionnaire/question.html'
					controller: 'questionnaireCtr'
]
