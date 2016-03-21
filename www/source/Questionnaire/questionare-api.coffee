angular.module 'PatientApp.Quest'

.factory 'QuestionAPI', ['$q', '$http', 'App', ($q, $http, App, $stateParams)->

	QuestionAPI = {}

	actionMode ={}


	QuestionAPI.getQuestion = (param)->

		defer = $q.defer()		
		App.SendParseRequest('startQuestionnaire', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise


	QuestionAPI.saveAnswer = (param)->
		
		defer = $q.defer()		
		App.SendParseRequest('getNextQuestion', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise


	QuestionAPI.getSummary = (param)->

		defer = $q.defer()		
		App.SendParseRequest('getSummary', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise



	QuestionAPI.submitSummary = (param)->
	
		defer = $q.defer()		
		App.SendParseRequest('submitQuestionnaire', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.deletAnswer = (param)->
	
		defer = $q.defer()		
		App.SendParseRequest('submitQuestionnaire2', param)
		.then (data)->
			defer.resolve data
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


	QuestionAPI.getNextQuest = (param)->

		defer = $q.defer()		
		App.SendParseRequest('getNextQuestion', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.getPrevQuest = (param)->

		defer = $q.defer()		
		App.SendParseRequest('getPreviousQuestion', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI	
]