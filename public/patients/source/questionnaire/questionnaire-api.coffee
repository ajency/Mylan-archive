angular.module 'angularApp.questionnaire'

.factory 'QuestionAPI', ['$q', '$http', 'App', ($q, $http, App)->
	QuestionAPI = {}

	QuestionAPI.getSummary = (options)->
		defer = $q.defer()			
		App.SendParseRequest('getSummary', options)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.getQuestion = (options)->
		defer = $q.defer()			
		App.SendParseRequest('startQuestionnaire', options)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise


	QuestionAPI.saveAnswer = (options)->
		defer = $q.defer()			
		App.SendParseRequest('getNextQuestion', options)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.submitSummary = (options)->
		defer = $q.defer()			
		App.SendParseRequest('submitQuestionnaire', options)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.getPrevQuest = (options)->
		defer = $q.defer()			
		App.SendParseRequest('getPreviousQuestion', options)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	QuestionAPI.getFirstQuest = (param)->

		defer = $q.defer()		
		App.SendParseRequest('goToFirstQuestion', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise



	QuestionAPI	

]