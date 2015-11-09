angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI'
	, ($scope, App, Storage, QuestionAPI)->

		$scope.view =
			title: 'C-weight'
			data : []

			navigate : ->
				App.navigate "questionnaire"

		
]


.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'dashboard',
			url: '/dashboard'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/dashboard/dashboard.html'
					controller: 'DashboardCtrl'

]
