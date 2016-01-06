app = angular.module('angularApp', ['ngRoute', 'angularApp.dashboard', 'angularApp.questionnaire'])



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

		.otherwise redirectTo: '/dashboard'
	
]





