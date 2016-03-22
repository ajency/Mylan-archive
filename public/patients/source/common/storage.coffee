angular.module 'angularApp.storage', []

.factory 'Storage', [->

	Storage = {}
	summaryData = {}
	questionnaireData = {}
	startQuestion = {}

	questStatus = ''

	Storage.summary = (action, data)->
	    switch action
	      when 'set'
	        summaryData = data

	      when 'get'
	        summaryData

    Storage.questionnaire = (action, data)->
    	
	    switch action
	      when 'set'
	        questionnaireData = data

	      when 'get'
	        questionnaireData

	Storage.startQuestionnaire = (action, data)->
    	
	    switch action
	      when 'set'
	        startQuestion = data

	      when 'get'
	        startQuestion

	Storage.getQuestStatus = (action,status)->
	    switch action
	      when 'set'
	        questStatus = status

	      when 'get'
	        questStatus 


	Storage

]
