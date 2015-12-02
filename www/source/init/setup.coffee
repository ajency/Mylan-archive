angular.module 'PatientApp.init'


.controller 'setupCtr', ['$scope', 'App', 'Storage','$ionicLoading','AuthAPI','CToast', 'CSpinner', 'LoadingPopup'
	, ($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, LoadingPopup)->
		
		$scope.view =
			refcode:''
			emptyfield:''
			deviceOS:''
			deviceType:''
			accessType:''
			deviceUUID:''
			last:''

			verifyRefCode : ->
				Storage.setRefernce('set', @refcode)
				# b = Storage.setRefernce('set', @refcode) No use of this code

				if @refcode =='' || _.isUndefined(@refcode)
					@emptyfield = "Please Enter Valid Reference Code"	
				else
					@deviceUUID = App.deviceUUID()

					if App.isAndroid() 
					 	@deviceOS = "Android"

					if App.isIOS() 
					 	@deviceOS = "IOS"

					CSpinner.show '', 'Please wait...'  
					AuthAPI.validateRefCode @refcode, @deviceUUID ,@deviceOS
					.then (data)=>
						Storage.setHospitalData 'set', data.hospitalData
						Storage.hospital_data 'set', data.hospitalData 
						Storage.setRefernce 'set', @refcode
						if data.code == 'do_login'
							CSpinner.hide() #remove this
							Storage.refcode 'set',@refcode
							.then ()->
								App.navigate "main_login"
						else if data.code == 'set_password'
							CSpinner.hide() #remove this
							Storage.refcode 'set',@refcode
							.then ()->
								App.navigate "setup_password"
						else if data.code == 'limit_exceeded'
							CSpinner.hide() #remove this
							CToast.show 'Cannot do setup more then 5 times'
						else 
							CSpinner.hide() #remove this
							CToast.show 'Please check reference code'

						# @navigateUser data.code, @refcode // Use this function can make code more clean

					, (error)=>
						CToast.show 'Please try again'
						CSpinner.hide() #remove this
					# Use finally function insted of hiding spinner everywhere
					# .finally ()->
					# 	CSpinner.hide()

			tologin : ->
					# Storage.setup 'get'
					# .then (value)->
					# 	goto = if _.isNull value then "setup" else "main_login"
					# 	App.navigate goto
						App.navigate "main_login"

			forgetRefcode:->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/error-view/Error-Screen-2.html'
					hideOnStateChange: true	

				# LoadingPopup.showLoadingPopup 'views/error-view/Error-Screen-2.html'

			HelpRefcode:->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/error-view/RefCode-help-1.html'
					hideOnStateChange: true

				# LoadingPopup.showLoadingPopup 'views/error-view/RefCode-help-1.html'

			hide:->
			        $ionicLoading.hide();
			        #hideOnStateChange: false # this code do nothing 	

			clear:->
				@emptyfield="" 

			# myFunction:($event)->
			# 	console.log '--'
			# 	a = $('#simple').val() 
			# 	console.log a
			# 	if(a.length > 3)
			# 	 	# console.log 'sd'
			# 	 	$event.preventDefault()
			# 	 	# $event.stopPropagation()


			#Use this function insted of using if else condition

			# navigateUser : (code, refrence)->
			# 	getRefrence : (state)->
			# 		Storage.refcode 'set',refcode
			# 		.then ()-> App.navigate state

			# 	switch code
			# 		when 'do_login' then getRefrence 'main_login'
			# 		when 'set_password' then getRefrence 'setup_password'
			# 		when 'limit_exceeded' then CToast.show 'Cannot do setup more then 5 times'
			# 		else CToast.show 'Please check reference code'



				
]
	
