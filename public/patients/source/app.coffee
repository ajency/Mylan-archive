app = angular.module('angularApp', ['ngRoute', 'angularApp.dashboard'
		, 'angularApp.questionnaire', 'angularApp.common', 'angularApp.notification'])



.run ['$rootScope', 'App', ($rootScope, App)->

	# Parse.initialize APP_ID, JS_KEY

	$rootScope.$on '$routeChangeSuccess', (event, current, previous, rejection)->

		if !_.isUndefined(current.$$route)
			App.currentState  = current.$$route.controller

		if !_.isUndefined(previous) 
			if !_.isUndefined(previous.$$route) 
				App.previousState  = previous.$$route.controller
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

		.when '/questionnaire/:respStatus/:responseId',
			url: '/questionnaire'
			templateUrl: 'patients/views/question.html'
			controller: 'questionnaireCtr'

		.when '/notification',
			url: '/notification'
			templateUrl: 'patients/views/notification.html'
			controller: 'notifyCtrl'


		.otherwise redirectTo: '/dashboard'
	
]





