angular.module 'PatientApp.Quest'

.controller 'ExitQuestionnaireCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI)->

		$scope.view =
			hospitalData : ''
			phone : ''

			exit :()->
				ionic.Platform.exitApp()

			init:()->
				Storage.setData 'hospital_details','get'
				.then (data)=>
					@phone = phone 
					App.callUs(data.phone)

			call:()->
				App.callUs(@phone)
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'exit-questionnaire',
			url: '/exit-questionnaire'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/questionnaire/exit.html'
					controller: 'ExitQuestionnaireCtrl'
					

]

			
