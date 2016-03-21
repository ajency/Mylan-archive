angular.module 'PatientApp.Quest'

.controller 'ExitQuestionnaireCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI', '$ionicPlatform', 'HospitalData'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI, $ionicPlatform, HospitalData)->

		$scope.view =
			hospitalData : ''
			phone : ''
			email : HospitalData.email

			exit :()->
				ionic.Platform.exitApp()

			init:()->
				Storage.setData 'hospital_details','get'
				.then (data)=>
					@phone = data.phone
					

			call:()->
				App.callUs(@phone)

		onDeviceBackExit = ->

			switch App.currentState
				when 'exit-questionnaire'
					App.navigate "dashboard", {}, {animate: false, back: false}
				else
					count = -1
					App.goBack count

		deregisterExit = null
		$scope.$on '$ionicView.enter', ->
			console.log '$ionicView.enter questionarie'
			deregisterExit = $ionicPlatform.registerBackButtonAction onDeviceBackExit, 1000
			# $ionicPlatform.onHardwareBackButton onDeviceBackExit
		
		$scope.$on '$ionicView.leave', ->
			console.log '$ionicView.leave exit ....'
			if deregisterExit then deregisterExit()
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
					resolve:
						HospitalData :($q, Storage)->
							defer = $q.defer()
							Storage.setData 'hospital_details', 'get'
							.then (data)->
								defer.resolve data
							defer.promise

					

]

			
