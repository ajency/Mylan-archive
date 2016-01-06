angular.module 'angularApp.questionnaire'

.controller 'questionnaireCtr', ['$scope', 'QuestionAPI', '$routeParams'
	, ($scope, QuestionAPI, $routeParams)->

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

				@display = 'loader'
				# Storage.setData 'refcode','get'
				# .then (refcode)=>
				# 	@refcode = refcode
				# 	Storage.setData 'patientData','get'
				# .then (patientData)=>

				@respStatus = $routeParams.respStatus
				if @respStatus == 'lastQuestion'
					# param =
					# 	"questionId" : ''
					# 	"options": []
					# 	"value": ""
					# Storage.setData 'responseId','get'
					# .then (responseId)=>	
					# 	param.responseId = responseId
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
					# 		@display = 'noError'
					# 	,(error)=>
					# 		@display = 'error'
					# 		console.log error
					# 		if error == 'offline'
					# 			CToast.showLongBottom 'Check net connection,answer not saved'
					# 		else
					# 			CToast.show 'Error ,try again'
						

				else if @respStatus == 'noValue'
					responseId = ''

					options =
						"responseId": responseId
						"questionnaireId": 'EK9UXPhvP0'
						"patientId": '00011121'

					QuestionAPI.getQuestion options
					.then (data)=>
						console.log 'inside then'
						console.log data
						@data = data
						# @pastAnswer()
						# Storage.setData 'responseId', 'set', data.result.responseId
						@display = 'noError'
					,(error)=>
						@display = 'error'
						@errorType = error

				else 
					# responseId = $stateParams.respStatus

					# options =
					# 	"responseId": responseId
					# 	"questionnaireId": patientData.id
					# 	"patientId": @refcode

					# QuestionAPI.getQuestion options
					# .then (data)=>
					# 	console.log 'inside then'
					# 	console.log data
					# 	@data = data.result
					# 	@pastAnswer()
					# 	Storage.setData 'responseId', 'set', data.result.responseId
					# 	@display = 'noError'
					# ,(error)=>
					# 	@display = 'error'
					# 	@errorType = error

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
						@variables()
						@data = []
						@data = data.result
						@readonly = true

						if !_.isEmpty(@data.hasAnswer)
							@hasAnswerShow()
							@readonly = @data.editable
						@pastAnswer()
						if !_.isUndefined(@data.status)
							App.navigate 'summary', summary:responseId
						@display = 'noError'					
					,(error)=>
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


			init : ->
				console.log 'insie questionnaire'
				@getQuestion()	

]