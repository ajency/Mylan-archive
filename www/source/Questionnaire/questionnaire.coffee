angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout'
	($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner, $q, $timeout)->

		$scope.view =
			# noError / error / loader
			pastAnswerDiv : 0
			title: 'C-weight'
			data : []
			go : ''
			response : ''
			actionValue : {}
			errorType : 'No network connection'
			display : 'error'
			
			getLocal :()->
				defer = $q.defer()
				Storage.getNextQuestion 'get'
				.then (details)->
					defer.resolve details
				defer.promise

			getQuestion :(questNo) ->
				@display = 'noError'
				data1 = ''

				Storage.setData 'patientData','get'
				.then (patientData)->
					
					# options = 
					# 	'projectId': patientData.project_id
					# 	'hospitalId': patientData.hospital.id
					# 	'patientId': patientData.patient_id
					options =
						"projectId": patientData.project_id
						"hospitalId":patientData.hospital.id
						"patientId":parseInt(patientData.patient_id)

					url = PARSE_URL+'/getQuestionnaire'
					param = options
					

					App.sendRequest(url, param,PARSE_HEADERS)
					.then (data)->
						console.log '****123***'
						console.log data
						$scope.view.data = data.data.result.question 
						@data1 = data.data.result.question 
						console.log $scope.view.data
						@display = 'noError'
					, (error)=>
						console.log 'error'

					console.log 'data 11'
					console.log data1


					# console.log '****11-1***'
					# console.log options

					# QuestionAPI.getQuestion options
					# .then (data)=>
					# 	console.log '****123***'
					# 	console.log data


# projectId: patientData.project_id
# 						hospitalId: patientData.patient_id
# 						patientId: patientData.patient_id

				# hospitalId: patientData.patient_id 
				# options = 
				# 	quizID: $stateParams.quizID
				# 	questNo: questNo

				# QuestionAPI.getQuestion options
				# .then (data)=>
				# 	@getLocal()
				# 	.then (result)=> 
				# 		value = result
				# 		value = parseInt(value)
				# 		if value == 1
				# 			data.questionType = 'mcq'
				# 		else if value == 2
				# 			data.questionType = 'scq'
				# 			data.questionTittle = 'Has your weight changed in the past month ?'
				# 			data.option =
				# 				0:
				# 				 id : '1'
				# 				 answer : 'No change'
				# 				 value : 'no_pain'
				# 				 checked: false
				# 				1:
				# 				 id : '2'
				# 				 answer : 'Lost upto 4 pounds'
				# 				 value : 'pain_present'
				# 				 checked: false
				# 		else
				# 				data.questionType = 'descr'

				# 			@data = data 
				# 	, (error)=>
				# 		console.log 'err'

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
				CSpinner.show '', 'Please wait..'
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
