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
			questionType: 'descr'
			questionTittle: 'what is your current Statement best describes your pain'
			option:
				0:
				 id : '1'
				 answer : 'No Pain'
				 value : 'no_pain'
				 checked: true
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
				1:
				 type:'number'
				 placeholder : 'St'
				2:
				 type:'number'
				 placeholder : 'St'


			# fields:
			# 	0:
			# 	placeHolder:'kgs'
			# 	type : 'numeber'
			# 	1:
			# 	placeHolder:'st'
			# 	type : 'numeber'
			# 	2:
			# 	placeHolder:'lbs'
			# 	type : 'numeber'

				
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