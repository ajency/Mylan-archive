angular.module 'PatientApp.Global', []


.factory 'App', [ '$state', '$ionicHistory', '$window', '$q', '$http'
	,( $state, $ionicHistory, $window, $q, $http)->

		App = 
			start: true
			validateEmail: /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/
			onlyNumbers: /^\d+$/
			menuEnabled : left: false, right: false
			previousState: ''
			currentState: ''

			navigate : (state, params={}, opts={})->
				if !_.isEmpty(opts)
					animate = if _.has(opts, 'animate') then opts.animate else false
					back    = if _.has(opts, 'back')    then opts.back    else false
					$ionicHistory.nextViewOptions
						disableAnimate: !animate
						disableBack   : !back
		
				$state.go state, params

			goBack : (count)->
				$ionicHistory.goBack count

			# check_inputlength : (ngmodel)->
			# 	# console.log  @ngmodel.toString().length
			# 	if @ngmodel.toString().length < 8
			# 		console.log  @ngmodel.toString().length
			# 	else	
			# 		event.preventDefault()	

			# check_passwordlength : (Passngmodel)->
			# 	# console.log  @Passngmodel.toString().length
			# 	if @Passngmodel.toString().length < 4
			# 		console.log  @Passngmodel.toString().length
					
			# 		event.preventDefault()	

			isAndroid : ->
				ionic.Platform.isAndroid()

			isIOS : ->
				ionic.Platform.isIOS()

			isWebView : ->
				ionic.Platform.isWebView()

			isOnline : ->
				if @isWebView() then $cordovaNetwork.isOnline()
				else navigator.onLine

			deviceUUID : ->
				if @isWebView() then device.uuid else 'DUMMYUUID'

			hideKeyboardAccessoryBar : ->
				if $window.cordova && $window.cordova.plugins.Keyboard
					$cordovaKeyboard.hideAccessoryBar true

			sendRequest :(url,params,headers,timeout)->
				defer = $q.defer()

				if !_.isUndefined(timeout)
					headers['timeout'] = timeout

				$http.post url,  params, headers
				.then (data)->
					defer.resolve data
				, (error)->
					defer.reject error

				defer.promise	


]

