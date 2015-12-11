angular.module 'PatientApp.Quest'

.factory 'QuestionAPI', ['$q', '$http', 'App', ($q, $http, App, $stateParams)->

	QuestionAPI = {}

	actionMode ={}

	QuestionAPI.getQuestion = (opts)->
		defer = $q.defer()

		questionId = ''

		if ! _.isUndefined(opts.questionId)
			questionId = opts.questionId

		params = 
			"userdId" : '55'
			"quizID": opts.quizID
			"questionId" : questionId

		# questionType : mcq /scq /descr 

		data = 
			questionId : '112'
			questionType: 'mcq'
			questionTittle: 'What is your current Statement best describes your pain ?'
			option:
				0:
				 id : '1'
				 answer : 'No Pain'
				 value : 'no_pain'
				 checked: false
				1:
				 id : '2'
				 answer : 'Pain present but not needed for pain killer'
				 value : 'pain_present'
				 checked: false
				2:
				 id : '3'
				 answer : 'Pain present, and i take ocassional pain releiving medication'
				 value : 'take_medication'
				 checked: false

			fields:
				0:
				 type:'number'
				 placeholder: 'kgs'
				 name : 'kgs'
				1:
				 type:'number'
				 placeholder : 'St'
				 name : 'St'
				2:
				 type:'number'
				 placeholder : 'lbs'
				 name : 'lbs'

			pastAnswer : 'Pain present, and i take ocassional pain releiving medication'
			submitedDate : '5-11-2015'
			previousAnswered : '1'
			previousQuestion : 'true'

		

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

		# 'type': 'nextQuestion' / summary

		data = 
			'type': 'summary'
			'quizID' : '111'

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
				 question : 'What is your current Statement best describes your pain ?'
				 answer : 'pain is present ,but not needed for pain killer'
				1:
				 question : 'Has your weight changed in the past month ?'
				 answer : 'No change'
				2:
				 question : 'Has your weight changed in the past month ?'
				 answer : 'No change'
				

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

	QuestionAPI.setAction = (action, data={})->
		switch action
			when 'set'
				_.each data, (val, index)->
					actionMode[index] = val
			when 'get'
				actionMode

	QuestionAPI.checkDueQuest = (opts)->
		defer = $q.defer()

		params = 
			"userdId" : '55'
			"quizID": opts.quizID

		# expired/paused from the response ..

		data = 'paused'

		defer.resolve data

		defer.promise


	QuestionAPI	
]