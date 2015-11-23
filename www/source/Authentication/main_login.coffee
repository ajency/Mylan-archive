angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage','refrencecodeValue','$ionicLoading'
	, ($scope, App, Storage,refrencecodeValue,$ionicLoading)->

		
		$scope.view =
			temprefrencecode :''
			loginerror: ''
			password:''
			refrencecode:''
			showPassword: false

			getrefcode :->
				Storage.refcode('get').then (value)->
						console.log value
				value	
				 

			refre :->
					console.log refrencecodeValue
					@refrencecode = refrencecodeValue

						
			mainlogin : ->
				if @refrencecode =='' || @password ==''
					@loginerror = "Please Enter the credentials "

				else	
					if  _.isUndefined(@refrencecode) || _.isUndefined(@password) 
						@loginerror = "Please Enter valid credentials "
					else
							
						Storage.login 'set'
						.then ->
						App.navigate "dashboard"
			
			cleardiv :->
				@loginerror =""

			forgetRefcodeorPass:->
					$ionicLoading.show
						scope: $scope
						templateUrl:'views/error-view/Error-Screen-3.html'
						hideOnStateChange: true	

			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false

		
							

			
]
