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
				@display = 'noError'
				
				Storage.setData 'patientData','get'
				.then (patientData)=>
					@patientId = patientData.patient_id

					options =
						"projectId": patientData.project_id
						"hospitalId": patientData.hospital.id
						"patientId":parseInt(patientData.patient_id)


					QuestionAPI.getQuestion options
					.then (data)=>
						console.log 'inside then'
						console.log data
						@data = data.result
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
					


				# console.log 'init'
				# @actionValue = QuestionAPI.setAction 'get'
				# console.log @actionValue
				# if _.isEmpty(@actionValue) || @actionValue.mode == 'next'
				# 	@getQuestion()
				# else
				# 	@getPrevQuestion()


			navigate : ->
				# value = 
				@getLocal()
				.then (result)=> 
					value = result
					# Storage.getNextQuestion('get').then (value) ->
					value = parseInt(value)
					value++
					Storage.getNextQuestion 'set' , value
					if value == 4
						CSpinner.hide()
						App.navigate 'summary', quizID: 111
					else

						Storage.getNextQuestion 'set' , value
						# $window.location.reload()

						$timeout ->
							CSpinner.hide()
							$window.location.reload()
						,500

		

			nextQuestion : ->
				console.log 'nextQuestion'


				console.log @data

				selectedvalue = []

				_.each @data.options, (opt)->
					if opt.checked == true
						selectedvalue.push opt.id

					# if _.contains opt.checked, true
					# 	selectedvalue.push opt.id

				console.log 'nextquestt'
				console.log selectedvalue




				if @data.question.type == 'single-choice'

					if @singleChoiceValue == ''
						CToast.show 'Please select atleast one answer'
					else
						options =
							"responseId" : @data.response
							"patientId": @patientId
							"questionId" : @data.question.id
							"options": [@singleChoiceValue]
							"value": ""

						Storage.setData 'responseId','set', @data.response	

						CSpinner.show '', 'Please wait..'

						QuestionAPI.saveAnswer options
						.then (data)=>
							
							CToast.show 'Your answer is saved'
							@display = 'loader'
							nextQuest =
								"questionnaireId" : @data.id
								"questionIds" : [@data.question.id]
								"patientId" : @patientId
								"responseId" : @data.response

							QuestionAPI.getNextQuest nextQuest
						.then (data)=>
							console.log '******'
							console.log 'next question'
							console.log data
							@data = []
							@data = data.result
							@display = 'noError'




							


							
						,(error)=>
							console.log 'inside save error'
							console.log error
							CToast.show 'Error in saving your answer'

						.finally ->
							CSpinner.hide()

					



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
				console.log 'onTapToRetry'

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reInit()

		$scope.$on '$ionicView.afterEnter', (event, viewData)->
			# $timeout ->
			# 	console.log 'timeoutt'
			# 	$scope.view.infoBox = false
			# , 300

		
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
