app = angular.module('angularApp', ['ngRoute', 'angularApp.dashboard'
		, 'angularApp.questionnaire', 'angularApp.common'])



.run ['$rootScope', ($rootScope)->

	# Parse.initialize APP_ID, JS_KEY

]

.config ['$routeProvider' , ($routeProvider)->

	$routeProvider
		.when '/dashboard',
			url: '/dashboard'
			templateUrl: 'patients/views/dashboard.html'
			controller: 'dashboardController'

		.when '/summary/:responseId',
			url: '/summary'
			templateUrl: 'patients/views/summary.html'
			controller: 'summaryController'

		.when '/start-questionnaire',
			url: '/start-questionnaire'
			templateUrl: 'patients/views/start-questionnaire.html'
			controller: 'StartQuestionnaireCtrl'

		.when '/questionnaire/:respStatus',
			url: '/questionnaire'
			templateUrl: 'patients/views/question.html'
			controller: 'questionnaireCtr'

		.otherwise redirectTo: '/dashboard'
	
]





