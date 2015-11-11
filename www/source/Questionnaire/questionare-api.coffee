angular.module 'PatientApp.Quest'

.factory 'QuestionAPI', ['$q', '$http', 'App', ($q, $http, App, $stateParams)->

	QuestionAPI = {}

	QuestionAPI.getQuestion = (opts)->
		defer = $q.defer()

		params = 
			"userdId" : '55'
			"quizID": opts.quizID

		data = 
			questionId : '112'
			questionType: 'mcq'
			questionTittle: 'which Statement best describes your pain'
			option:
				0:
				 id : '1'
				 answer : 'No Pain'
				1:
				 id : '2'
				 answer : 'Pain present but not needed for pain killer'
				2:
				 id : '3'
				 answer : 'Pain present, and i take ocassional pain releiving medication'
				
			pastAnswer : 'Pain present, and i take ocassional pain releiving medication'
			submitedDate : '5-11-2015'

		defer.resolve data

		defer.promise

	QuestionAPI.saveAnswer = (opts)->
		defer = $q.defer()

		params = 
			"userdId" : '55'
			"quizID": opts.quizID
			"questionId" : opts.questionId
			"answerId" : opts.answerId
			"action" : opts.action

		# 'type': 'nextQuestion'

		data = 
			'type': 'summary'
			'quizID' : '111'

		console.log '***********'

		console.log params


		defer.resolve data

		defer.promise


	QuestionAPI.getSummary = (opts)->
		defer = $q.defer()

		params = 
			"userdId" : '55'
			"quizID": opts.quizID

		data =
			summary:
				0:
				 question : 'Which statement best describes your pain'
				 answer : 'pain is present ,but not needed for pain killer'
				1:
				 question : 'Which statement best describes your pain'
				 answer : 'pain is present ,but not needed for pain killer'
				2:
				 question : 'Which statement best describes your pain'
				 answer : 'pain is present ,but not needed for pain killer'
				

		defer.resolve data

		defer.promise

	QuestionAPI.submitSummary = (opts)->
		defer = $q.defer()

		params = 
			"userdId" : '55'
			"quizID": opts.quizID

		data = 'success'

		defer.resolve data

		defer.promise


	QuestionAPI	
]