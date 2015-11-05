
angular.module 'PatientApp', ['ionic', 'PatientApp.init', 'PatientApp.storage','PatientApp.Global']




.run ['$rootScope', 'App', 'User', '$timeout', ($rootScope, App, User, $timeout)->


	$rootScope.App = App
	App.navigate 'init'

]

.config ['$stateProvider', ($stateProvider)->


	
]