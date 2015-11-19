
angular.module 'PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage'
			,'PatientApp.Global','PatientApp.Auth','PatientApp.Quest'
			, 'PatientApp.main', 'PatientApp.dashboard']




.run ['$rootScope', 'App', 'User', '$timeout', ($rootScope, App, User, $timeout)->


	$rootScope.App = App
	App.navigate 'init', {}, {animate: false, back: false}

]

.config ['$stateProvider', ($stateProvider)->


	
]