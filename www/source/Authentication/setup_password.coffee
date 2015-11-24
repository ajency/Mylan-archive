angular.module 'PatientApp.Auth',[]

.controller 'setup_passwordCtr',['$scope', 'App', 'Storage','$ionicLoading','AuthAPI', 'CToast', 'CSpinner'
	, ($scope, App, Storage,$ionicLoading,AuthAPI, CToast, CSpinner)->


		  	
		$scope.view =
			New_password:''
			Re_password: ''
			passwordmissmatch:''
			hospitalName : ''
			projectName : ''

			
			init: ->
				value = Storage.setHospitalData 'get'
				@hospitalName = value['name']
				@projectName = value['project']

			completesetup : ->
				
					console.log @New_password
					console.log @Re_password

					if (@New_password =='' ||  @Re_password =='' ) || ((_.isUndefined(@New_password) && _.isUndefined(@New_password)))
						@passwordmissmatch = "Please Enter Valid 4 digit password"
								
					else			
						if angular.equals(@New_password, @Re_password)

	                        Storage.setup 'set'
	                        .then ->
	                          console.log 'setup done'
	                          App.navigate "main_login"
	                        if App.isWebView()
	                        	CSpinner.show '', 'Checking credentials please wait'
	                        	refrencecode = Storage.setRefernce('get')

		                        AuthAPI.setPassword(refrencecode, @Re_password)
		                        .then (data)=>
		                        	CSpinner.hide()
		                        	console.log data
		                        	App.navigate "main_login"
		                        , (error)=>
		                        	CToast.show 'Please try again'
		                        	CSpinner.hide()
	                        else
	                        	  
	                        	Storage.setup 'set'
	                          	.then ->
		                          console.log 'setup done'
		                          App.navigate "main_login"

	                        

	                        # Storage.setup 'set'
	                        #   .then ->
	                        #   console.log 'setup done'
	                        #   App.navigate "main_login"
	                        	
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
					
		# $scope.$on '$ionicView.beforeEnter', (event, viewData)->

		# 	value = Storage.setHospitalData 'get'
		# 	$scope.view.hospitalName = value['name']
		# 	$scope.view.projectName = value['project']



					




		 



]
