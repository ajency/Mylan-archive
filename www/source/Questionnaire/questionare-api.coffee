angular.module 'PatientApp.Quest'

.factory 'QuestionAPI', ['$q', '$http', 'App', ($q, $http, App)->

	QuestionAPI = {}

	QuestionAPI.get = ()->

		params = 
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

		params

	QuestionAPI	
]