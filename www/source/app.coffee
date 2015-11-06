
angular.module 'PatientApp', ['ionic', 'PatientApp.init', 'PatientApp.storage'
			,'PatientApp.Global','PatientApp.Auth','PatientApp.Quest'
			, 'PatientApp.main', 'PatientApp.dashboard']




.run ['$rootScope', 'App', 'User', '$timeout', ($rootScope, App, User, $timeout)->


	$rootScope.App = App
	App.navigate 'init'

]

.config ['$stateProvider', ($stateProvider)->


	
]