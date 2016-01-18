angular.module 'PatientApp.contact',[]

.controller 'contactCtrl',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		$scope.view =
			pastAnswerDiv : 0

			

			call:()->
				Storage.setData 'hospital_details','get'
				.then (data)=>
					App.callUs(data.phone)




]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'contact',
			url: '/contact'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/contact/contact.html'
					controller: 'contactCtrl'
					

]

			
