angular.module 'PatientApp.Quest',[]

.controller 'questionnaireCtr',['$scope', 'App', 'Storage', 'QuestionAPI'
	, ($scope, App, Storage, QuestionAPI)->

		$scope.view =
			title: 'C-weight'
			data : []

			getQuestion : ->
				QuestionAPI.get()


			init : ->
				@data = @getQuestion()
			
]


.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'questionnaire',
			url: '/questionnaire'
			templateUrl: 'views/questionnaire/question.html'
			controller: 'questionnaireCtr'

]
