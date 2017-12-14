angular.module 'PatientApp.contact',[]

.controller 'contactCtrl',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		$scope.view =
			pastAnswerDiv : 0
			hospitalDetails: null
			init: () ->
				Storage.setData 'hospital_details','get'
				.then (data) =>
					@hospitalDetails = data
					$scope.$apply () -> {}
	

			

			call:()->
				App.callUs(@hospitalDetails.phone)




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

			
