angular.module 'angularApp.questionnaire'

.factory 'QuestionAPI', ['$q', '$http', 'App', ($q, $http, App)->
	QuestionAPI = {}

	QuestionAPI.getSummary = (id)->
		defer = $q.defer()

		PARSE_URL = 'https://api.parse.com/1/functions'

		url = PARSE_URL+'/getSummary'

		# 6xS787uoTf

		param =
			'responseId' : id

		PARSE_HEADERS =
			headers:
				"X-Parse-Application-Id" : 'MQiH2NRh0G6dG51fLaVbM0i7TnxqX2R1pKs5DLPA'
				"X-Parse-REST-API-KeY" : 'I4yEHhjBd4e9x28MvmmEOiP7CzHCVXpJxHSu5Xva'
				
		$http.post url,  param, PARSE_HEADERS
		.then (data)->
			defer.resolve data.data
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



	QuestionAPI	

]