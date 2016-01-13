angular.module 'PatientApp.Auth',[]

.controller 'setup_passwordCtr',['$scope', 'App', 'Storage','$ionicLoading','AuthAPI'
	, 'CToast', 'CSpinner', 'HospitalData'
	, ($scope, App, Storage,$ionicLoading,AuthAPI, CToast, CSpinner, HospitalData)->
  	
		$scope.view =
			New_password:''
			Re_password: ''
			passwordmissmatch:''
			hospitalName : HospitalData.name
			projectName : HospitalData.project

			reset :()->
				@New_password = ''
				@Re_password = ''
				@passwordmissmatch = ''

			completesetup : ->
					if (@New_password =='' ||  @Re_password =='' ) || ((_.isUndefined(@New_password) && _.isUndefined(@New_password)))
						@passwordmissmatch = "Please Enter Valid 4 digit password"		
					else			
						if angular.equals(@New_password, @Re_password)
                        	CSpinner.show '', 'Please wait..'
                        	Storage.setData 'refcode', 'get'
                        	.then (refcode) =>
                        		console.log refcode
                        		console.log App.previousState
		                        AuthAPI.setPassword(refcode, @Re_password)
		                        .then (data)=>
		                        	console.log data
		                        	if App.previousState == 'setup' then  App.navigate "main_login" else CToast.show 'Your password is updated '
		                        , (error)=>
		                        	CToast.show 'Please try again'
		                        .finally ()->
		                        	CSpinner.hide()
						else	
							@passwordmissmatch = 'Passwords Do Not Match, Please Enter Again.'

			clear:->
				@passwordmissmatch= ""	

			passwordHelp:->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/error-view/Password-help.html'
					hideOnStateChange: true						
			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false	
					
		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reset();

]
