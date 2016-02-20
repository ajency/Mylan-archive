angular.module 'angularApp.questionnaire'

.controller 'questionnaireCtr', ['$scope', 'QuestionAPI', '$routeParams', 'CToast', '$location', 'Storage'
	, ($scope, QuestionAPI, $routeParams, CToast, $location, Storage)->

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
			limitTo : 5
			showMoreButton : true
			popTitle : ''
			responseId : ''

			overlay : false

			showMore : ->
				@limitTo = @limitTo + 5
				App.resize()
				if @data.length < @limitTo 
					@showMoreButton = false


			CSpinnerShow : ()->
				@overlay = true;

			isEmpty :(pastAnswerObject)->
				_.isEmpty(pastAnswerObject)	

			CSpinnerHide :()->
				@overlay = false;


			variables :()->
				@descriptiveAnswer = ''
				@singleChoiceValue = ''
				@val_answerValue = {}

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
					@val_answerValue[ObjId.option] = Number(@data.hasAnswer.value)


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

			getQuestion :() ->

				startQuestData = {}
				Storage.startQuestionnaire 'set', startQuestData

				questionnaireData = Storage.questionnaire 'get'
				console.log '**************getQuestion**************'
				console.log questionnaireData
				if !_.isEmpty questionnaireData 

					@display = 'loader'
					
					@respStatus = questionnaireData.respStatus
					@responseId = questionnaireData.responseId
					

					if @respStatus == 'lastQuestion'
						param =
							"questionId" : ''
							"options": []
							"value": ""
							"responseId" : questionnaireData.responseId 


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
							@checkQuestinarieStatus(data)
						,(error)=>
							@display = 'error'
							console.log error
							if error == 'offline'
								CToast.show 'Check net connection,answer not saved'
							else
								CToast.show 'Error ,try again'
							

					else if @respStatus == 'noValue'
						responseId = ''

						options =
							"responseId": responseId
							"questionnaireId": questionnaireIdd
							"patientId": RefCode

						QuestionAPI.getQuestion options
						.then (data)=>
							console.log 'inside then'
							console.log data
							@data = data

							@pastAnswer()
							# Storage.setData 'responseId', 'set', data.result.responseId
							@display = 'noError'
						
						,(error)=>
							@display = 'error'
							@errorType = error

					else 
						responseId = questionnaireData.responseId 

						options =
							"responseId": responseId
							"questionnaireId": questionnaireIdd
							"patientId": RefCode

						QuestionAPI.getQuestion options
						.then (data)=>
							console.log 'inside then'
							console.log data
							@data = data
							@pastAnswer()
							@display = 'noError'
							@checkQuestinarieStatus(data)
						,(error)=>
							@display = 'error'
							@errorType = error
				else
				  $location.path 'dashboard'
				  			
			loadNextQuestion :(param)->

				@responseId = param.responseId
				@CSpinnerShow()
				QuestionAPI.saveAnswer param
				.then (data)=>
					# App.resize()
					# if @readonly == true then CToast.show 'Your answer is saved'
					console.log '******next question******'
					console.log data
					

					@variables()
					@data = []
					@data = data
					@readonly = true

					if !_.isEmpty(@data.hasAnswer)
						@hasAnswerShow()
						@readonly = @data.editable
					@pastAnswer()
					
					@display = 'noError'
					@checkQuestinarieStatus(data)
				,(error)=>
					if error == 'offline'
						CToast.show 'Check net connection,answer not saved'
					else
						CToast.show 'Error in saving answer,try again'
				.finally ()=>
					@CSpinnerHide()
						

			nextQuestion : ->

				
		
				if @data.questionType == 'single-choice'

					if @singleChoiceValue == ''
						CToast.show 'Please select atleast one answer'
					else
						options =
							"questionId" : @data.questionId
							"options": [@singleChoiceValue]
							"value": ""
							"responseId" : @data.responseId

						@loadNextQuestion(options)

				if @data.questionType == 'input'

					error = 0
					sizeOfField = _.size(@data.options)
					sizeOfTestboxAns = _.size(@val_answerValue)

					if (sizeOfTestboxAns == 0)
						error = 1
					else
						_.each @val_answerValue, (value)->
							value = value.toString()
							valid = (value.match(/^(?![0.]+$)\d+(\.\d{1,2})?$/gm));

							if value == null || valid == null
								error = 1

					if error == 1
						CToast.show 'Please enter numbers'
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
							"responseId" : @data.responseId

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
							"responseId" : @data.responseId

						@loadNextQuestion(options)

				if @data.questionType == 'descriptive'

					if (@descriptiveAnswer == '')
						CToast.show 'Please Fill in the following'
					else
						options =
							"questionId" : @data.questionId
							"options": []
							"value": @descriptiveAnswer
							"responseId" : @data.responseId

						@loadNextQuestion(options)


			loadPrevQuestion :(param)->
					
				@CSpinnerShow()
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
					console.log @data
					@checkQuestinarieStatus(data)
				,(error)=>
					console.log error
					if error == 'offline'
						CToast.show 'Check net connection,answer not saved'
					else
						CToast.show 'Error ,try again'
				.finally ()=>
					@CSpinnerHide()


			prevQuestion : ->

				if @data.questionType == 'single-choice'
					
					options =
						"responseId" : @data.responseId
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
						"responseId" : @data.responseId
						"questionId" : @data.questionId
						"options": if selectedvalue == [] then [] else selectedvalue
						"value": ""

					@loadPrevQuestion(options)

				if @data.questionType == 'descriptive'
					options =
						"responseId" : @data.responseId
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
						"responseId" : @data.responseId
						"questionId" : @data.questionId
						"options": optionId
						"value": value.toString()

					@loadPrevQuestion(options)

			onTapToRetry : ->
				
				@display = 'loader'
				@getQuestion()


			init : ->
				console.log 'insie questionnaire'
				@getQuestion()	

			closeModal : ->
				$('#pauseModal').modal('hide')
				$('.modal-backdrop').addClass('hidden')
				$("body").removeClass("modal-open")
				$location.path('dashboard')

			showConfirm : ->
				console.log 'popup shown '
				$('#QuestionarieModal').modal('show')

			CloseQUestionPopup : ->
				$('#QuestionarieModal').modal('hide')
				$('.modal-backdrop').addClass('hidden')
				$("body").removeClass("modal-open")
				$location.path('dashboard')

			checkQuestinarieStatus:(data)->
				
				if !_.isUndefined(data.status)
					if data.status == 'completed'
						@popTitle = 'This questionnaire was Completed'
						@showConfirm()
						@display = 'completed'
					else if data.status == 'missed'
						@popTitle = 'This questionnaire was Missed'
						@showConfirm()
						@display = 'completed'
					else if data.status == 'saved_successfully'
						summaryData = 
							previousState : 'questionnaire'
							responseId : @responseId

						Storage.summary 'set', summaryData
						$location.path('summary')




]