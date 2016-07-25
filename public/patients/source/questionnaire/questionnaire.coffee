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
			email: hospitalEmail
			firstText : ''
			secondText : ''
			phone : hospitalPhone

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
				@firstText = 'notSelected'
				@secondText = 'notSelected'
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
					kgsSelected = []
					_.each @data.hasAnswer.option, (value)=>
						str = value.label
						str = str.toLowerCase()	
						labelKg = ['kg', 'kgs']
						bool = _.contains(labelKg, str)
						
						if bool	== true
							kgsSelected.push(1)


					if kgsSelected.length == 0
						@firstText = 'notSelected'
						@secondText = 'selected'
					else
						@firstText = 'selected'
						@secondText = 'notSelected'

					_.each @data.hasAnswer.option, (val) =>
						@val_answerValue[val.label] = Number(val.value)



			pastAnswer:()->
				previousAns = @data.previousQuestionnaireAnswer

				if !_.isEmpty(previousAns)

					if @data.questionType == 'input'
						console.log '1'
						# if !_.isEmpty previousAns.optionId[0] 
						# 	ObjId = _.findWhere(@data.options, {id: previousAns.optionId[0]})
						# 	ObjId.option
						# 	@data.previousQuestionnaireAnswer['label'] = ObjId.option

					if @data.questionType == 'single-choice' || @data.questionType == 'multi-choice'
						optionSelectedArray = []
						sortedArray = _.sortBy( @data.options, 'score' )
						pluckId = _.pluck(sortedArray, 'id')
						_.each previousAns.optionId, (value) =>
							a = _.indexOf(pluckId, value)
							if a != -1
								a++
								optionSelectedArray.push(a)

						optionSelectedArray.sort()
						
						@data.previousQuestionnaireAnswer['label'] = optionSelectedArray.toString()

					@data.previousQuestionnaireAnswer.dateDisplay = moment(previousAns.date).format('MMMM Do YYYY')

			vlaidateInput : ->

				InputReturn = true
				valueArr = []
				validArr = []
				error = 0
				sizeOfField = _.size(@data.options)
				sizeOfTestboxAns = _.size(@val_answerValue)

				kgValid =  false
				lbValid = true
				stValid = false
				weightInput = 0

				if (sizeOfTestboxAns == 0)
					error = 1
				else
					_.each @val_answerValue, (value)->
						value = value.toString()
						if value == null || value == ''
							valueArr.push 1
						else
							valid = (value.match(/^(?![0.]+$)\d+(\.\d{1,2})?$/gm))
							if valid == null
								validArr.push 1

					if valueArr.length == _.size(@val_answerValue)
						error = 1
				#for lbs validation 
				if !_.isEmpty @val_answerValue
					weightKeys = _.keys @val_answerValue
					weigthValueArray = _.values @val_answerValue

					_.each weightKeys, (val)->
						lowerCase = val.toLowerCase()
						if _.contains ['kg','kgs'], lowerCase
							weightInput = 1
							valid = (weigthValueArray[_.indexOf weightKeys,val].toString().match(/^(?![0.]+$)\d+(\.\d{1,2})?$/gm))
							if valid != null
								kgValid = true

						# weightKeyArray.push val.toLowerCase()
						lowerCase = val.toLowerCase()
						if _.contains ['lb','lbs'], lowerCase
							weightInput = 1
							valid = (weigthValueArray[_.indexOf weightKeys,val].toString().match(/^-?\d*(\.\d+)?$/))
							if valid == null
								lbValid = false

						lowerCase = val.toLowerCase()
						if _.contains ['st','sts'], lowerCase
							weightInput = 1
							valid = (weigthValueArray[_.indexOf weightKeys,val].toString().match(/^(?![0.]+$)\d+(\.\d{1,2})?$/gm))
							if valid != null 
								stValid = true
							else if valid == null
								stValid = false
		
				# ***temp**
				if (weightInput == 0) && (error == 1 || validArr.length > 0)
					CToast.show 'Please enter the values'
				else if (weightInput == 1) && (@firstText == 'selected' && kgValid == false)
					CToast.show 'Please enter valid value,kg cannot be zero'
				else if (weightInput == 1) && (@secondText == 'selected' && (stValid == false || lbValid == false ))
					CToast.show 'Please enter valid value,st cannot be zero'
				else
					valueInput = []
					optionId = []
					arryObj = []
					_.each @data.options, (opt)=>
						obj={}
						a = @val_answerValue[opt.option]
						if !_.isUndefined(a) && a !=''
							obj['id'] = opt.id
							obj['value'] = a.toString()
							arryObj.push(obj)

					options =
						"questionId" : @data.questionId
						"options": arryObj
						"value": ""
						"responseId" : @data.responseId

					InputReturn = options

				return InputReturn


			
			getQuestion :() ->
				Storage.getQuestStatus('set','')
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
							@display = 'noError'
							@checkQuestinarieStatus(data)
							console.log 'previous data'
							console.log @data	
							@variables()
							@data = []
							@data = data
							@questionLabel()
							@readonly = @data.editable
							@pastAnswer()
							if !_.isEmpty(@data.hasAnswer)
								@hasAnswerShow()	
							
						,(error)=>
							@display = 'error'
							console.log error
							if error == 'offline'
								CToast.show 'Check net connection,answer not saved'
							else
								CToast.show 'Error ,try again'

					else if @respStatus == 'firstQuestion'
						param =
							"questionnaireId": questionnaireIdd
							"responseId" : questionnaireData.responseId 

						QuestionAPI.getFirstQuest param
						.then (data)=>
							@display = 'noError'
							@checkQuestinarieStatus(data)
							console.log 'previous data'
							console.log @data	
							@variables()
							@data = []
							@data = data
							@questionLabel()
							@readonly = @data.editable
							@pastAnswer()
							if !_.isEmpty(@data.hasAnswer)
								@hasAnswerShow()	
							
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
							@checkQuestinarieStatus(data)
							@pastAnswer()
							# Storage.setData 'responseId', 'set', data.result.responseId
							@display = 'noError'
						
						,(error)=>
							if error == 'offline'
								Storage.getQuestStatus('set','offline')
								$location.path 'dashboard'	
							else
								Storage.getQuestStatus('set','questionnarireError')
								$location.path 'dashboard'	

							
							# @display = 'error'
							# @errorType = error

					else 
						responseId = questionnaireData.responseId 

						options =
							"responseId": responseId
							"questionnaireId": questionnaireIdd
							"patientId": RefCode

						QuestionAPI.getQuestion options
						.then (data)=>
							@display = 'noError'
							@checkQuestinarieStatus(data)
							console.log 'inside then'
							console.log data
							@data = data
							@questionLabel()
							@pastAnswer()	
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
					@display = 'noError'
					# App.resize()
					# if @readonly == true then CToast.show 'Your answer is saved'
					console.log '******next question******'
					console.log data
					if !_.isUndefined(data.status)
						@checkQuestinarieStatus(data)
					else
						@variables()
						@data = []
						@data = data
						@questionLabel()
						@readonly = true

						if !_.isEmpty(@data.hasAnswer)
							@hasAnswerShow()
							@readonly = @data.editable
						@pastAnswer()	
					
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

					param = @vlaidateInput() 
					if param != true then @loadNextQuestion(param)

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
					@checkQuestinarieStatus(data)
					console.log 'previous data'
					console.log @data	
					@variables()
					@data = []
					@data = data
					@questionLabel()
					@readonly = @data.editable
					@pastAnswer()
					if !_.isEmpty(@data.hasAnswer)
						@hasAnswerShow()	
					console.log @data
					
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
					containsString  = 0
					if _.isEmpty(@val_answerValue)|| _.isUndefined(@val_answerValue)
						containsString = 0	
					else
						_.each @val_answerValue, (value)->
							if value.toString() != ''
								containsString = 1

					if containsString == 0
						options =
							"responseId" : @data.responseId
							"questionId" : @data.questionId
							"options": []
							"value": ""
						@loadPrevQuestion(options)
					else 
						param = @vlaidateInput() 
						if param != true then @loadPrevQuestion(param)

			onTapToRetry : ->
				if @respStatus == 'noValue'
					 $location.path 'dashboard'
				else
					@display = 'loader'
					@getQuestion()


			init : ->
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
						@popTitle = 'This questionnaire was completed'
						@showConfirm()
						@display = 'completed'
					else if data.status == 'missed'
						@popTitle = 'This questionnaire was missed'
						@showConfirm()
						@display = 'completed'
					else if data.status == 'saved_successfully'
						summaryData = 
							previousState : 'questionnaire'
							responseId : @responseId

						Storage.summary 'set', summaryData
						$location.path('summary')

					else if data.status == 'already_taken'
						Storage.getQuestStatus('set','already_taken')
						$location.path('dashboard')

			questionLabel:()->
				if @data.questionType == 'input'
					arr = []
					@data.withoutkg = {}
					@data.withkg = {}
					kg = {}

					_.each @data.options, (value)=>
						str = value.option
						str = str.toLowerCase()	
						labelKg = ['kg', 'kgs']
						bool = _.contains(labelKg, str)
						
						if bool	
						  arr.push 1
						  kg = value
						 
					if arr.length > 0
						@data.optionsLabel = true
						@data.withoutkg = _.without(@data.options, kg)
						@data.withkg = kg
					else
						@data.optionsLabel = false


			firstRow:()->
				if @readonly == false && !_.isEmpty @data.hasAnswer
					edit = true
				else
					edit = false
				if edit == false
					@firstText = 'selected'
					@secondText = 'notSelected'
					
					a = {}
					_.each @val_answerValue, (val,key) =>
						a[key] = ''
					@val_answerValue = a					

			secondRow:()->
				if @readonly == false && !_.isEmpty @data.hasAnswer
					edit = true
				else
					edit = false
				if edit == false
					@firstText = 'notSelected '
					@secondText = 'selected'
					
					_.each @data.options, (value)=>
						str = value.option
						str = str.toLowerCase()	
						labelKg = ['kg', 'kgs']
						bool = _.contains(labelKg, str)	

						if bool	
						  if !_.isUndefined(@val_answerValue)
						  	@val_answerValue[value.option] = ''






]