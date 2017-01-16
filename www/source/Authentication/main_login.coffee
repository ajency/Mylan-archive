angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	 ,'$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', '$ionicPlatform', 'RefcodeData'
	 , ($scope, App, Storage,
	 	 $ionicLoading, AuthAPI, CToast, CSpinner, $ionicPlatform, RefcodeData)->
		MYLANPHONE = '1234567891'
		$scope.view =
			temprefrencecode :''
			loginerror: ''
			password:''
			readonly : ''
			refrencecode : RefcodeData
			

			mainlogin : ->
				if @refrencecode =='' || @password ==''
					@loginerror = "Please enter your credentials "
				else	
					if  _.isUndefined(@refrencecode) || _.isUndefined(@password) 
						@loginerror = "Please enter valid credentials "
					else
						CSpinner.show '', 'Checking credentials please wait'
						AuthAPI.validateUser(@refrencecode,@password )
						.then (data)=>
							
							if data.code == 'successful_login'
								Parse.User.become data.user
								.then (user)=>
									Storage.setData 'logged','set', true
								.then ()=> 
									Storage.setData 'refcode','set', @refrencecode
								.then ()=>
									Storage.setData 'hospital_details', 'set', data.hospital
								.then ()=>
									Storage.setData 'patientData', 'set', data.questionnaire
								.then ()=>
									App.navigate "dashboard", {}, {animate: false, back: false}
								, (error)->
									console.log error
							else if data.code == 'limit_exceeded'
								@loginerror = 'Cannot do setup more then 10 times'
							else if data.code == 'invalid_login'
								@password = ''
								if @readonly == false 
									 @refrencecode = ''
								@loginerror = 'Credentials entered are invalid'
							else if data.code == 'password_not_set'
								@loginerror = 'No password set for the reference code'
							else if data.code == 'baseline_not_set'
								@loginerror = 'Patient cannot be activated,due to missing activation data . Please contact your hospital administrator'
							else if data.code == 'project_paused'
								@loginerror = 'This project is paused. Please contact your hospital administrator'
							else if data.code == 'login_attempts'
								@loginerror = 'You have exceeded the maximum login attempts.Please contact your hospital administrator'
							else if data.code == 'inactive_user'
								@loginerror = 'This patient is inactive. Please contact your hospital administrator'
							else
								CToast.show 'Please check credentials'
								@loginerror = "Password entered is incorrect, Please try again"
						, (error)=>
							if error == 'offline'
								@loginerror = 'Please check net connection'
							else if error == 'server_error'
								@loginerror = 'Please try again'
						.finally ()->
							CSpinner.hide()

			cleardiv :->
				@loginerror = ""

			forgetRefcodeorPass:->
					$ionicLoading.show
						scope: $scope
						templateUrl:'views/error-view/Error-Screen-3.html'
						hideOnStateChange: true	

			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false

			reset:->
				@loginerror = ''
				@password = ''

			call:()->
				App.callUs(MYLANPHONE)

			onDeviceBack:->
				if App.previousState == 'setup_password'
					App.navigate "setup", {}, {animate: false, back: false}
				else
					count = -1
					App.goBack count



		onDeviceBack = ->
			if App.previousState == 'setup_password'
				App.navigate "setup", {}, {animate: false, back: false}
			else
				count = -1
				App.goBack count

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			if !viewData.enableBack
				viewData.enableBack = true
				
			$scope.view.reset();
			Storage.setData 'refcode', 'get'
			.then (refcode)=>
				$scope.view.refrencecode = refcode
				if $scope.view.refrencecode == null
					$scope.view.readonly = false
				else
					$scope.view.readonly = true

		onHardwareBackLogin = null 

		$scope.$on '$ionicView.enter', ->
			onHardwareBackLogin = $ionicPlatform.registerBackButtonAction onDeviceBack, 1000
		
		$scope.$on '$ionicView.leave', ->
			if onHardwareBackLogin then onHardwareBackLogin()
		
]
