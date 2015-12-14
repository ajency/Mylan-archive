angular.module 'PatientApp.Quest'

.factory 'QuestionAPI', ['$q', '$http', 'App', ($q, $http, App, $stateParams)->

	QuestionAPI = {}

	actionMode ={}


	QuestionAPI.getQuestion = (options)->

		defer = $q.defer()
		# getQuestionnaire
		
		url = PARSE_URL+'/startQuestionnaire'
		param = options
				
		App.sendRequest(url, param,PARSE_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise


	QuestionAPI.saveAnswer = (options)->
		
		defer = $q.defer()

		url = PARSE_URL+'/getNextQuestion'
		param = options
				
		App.sendRequest(url, param,PARSE_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise


	QuestionAPI.getSummary = (opts)->
		
		defer = $q.defer()

		url = PARSE_URL+'/getSummary'
		param = opts
				
		App.sendRequest(url, param, PARSE_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.submitSummary = (opts)->
		defer = $q.defer()

		url = PARSE_URL+'/submitQuestionnaire'
		param = opts
				
		App.sendRequest(url, param, PARSE_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
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


	QuestionAPI.getNextQuest = (options)->

		defer = $q.defer()

		url = PARSE_URL+'/getNextQuestion'
		param = options
				
		App.sendRequest(url, param, PARSE_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.getPrevQuest = (options)->

		defer = $q.defer()

		url = PARSE_URL+'/getPreviousQuestion '
		param = options
				
		App.sendRequest(url, param, PARSE_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise





	QuestionAPI	
]