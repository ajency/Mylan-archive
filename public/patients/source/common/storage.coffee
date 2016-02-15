angular.module 'angularApp.storage', []

.factory 'Storage', [->

	Storage = {}
	summaryData = {}
	questionnaireData = {}

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


	Storage

]
