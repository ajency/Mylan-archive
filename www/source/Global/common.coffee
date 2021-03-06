angular.module 'PatientApp.Global', []


.factory 'App', [ '$state', '$ionicHistory', '$window', '$q', '$http', '$cordovaNetwork'
	, '$cordovaPreferences', '$ionicScrollDelegate'
	,( $state, $ionicHistory, $window, $q, $http, $cordovaNetwork, $cordovaPreferences, $ionicScrollDelegate)->

		App = 
			start: true
			validateEmail: /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/
			onlyNumbers: /^\d+$/
			menuEnabled : left: false, right: false
			ContactUsEnabled : true
			UpdatesEnabled: true
			resetPassword : true
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

			errorCode : (error) ->
				error = ''
				if error.status == '0'
					error = 'timeout'
				else
					error = 'server_error'
				error	

			sendRequest :(url,params,headers,timeout)->
				defer = $q.defer()

				if !_.isUndefined(timeout)
					headers['timeout'] = timeout

				if @isOnline()	
					$http.post url,  params, headers
					.then (data)->
						defer.resolve data
					, (error)=>
						defer.reject @errorCode(error)
				else
					defer.reject 'offline'

				defer.promise

			# cordova prefernce plugin need to be edit done for test purpose..

			cordovaPreference :(key, myMagicValue)->
				defer = $q.defer()

				$cordovaPreferences.store(key, myMagicValue)
				.then (data)->
					console.log 'cordovva'
					console.log data
					defer.resolve data
				, (error)->
					console.log error
					defer.reject error

			reteriveCordovaPreference :()->
				defer = $q.defer()
				$cordovaPreferences.fetch('int')
				.then (data)->

					console.log 'sucess data--'+data
					defer.resolve data
				, (error)=>
					defer.reject error


			resize : ->
				$ionicScrollDelegate.resize()

			getInstallationId : ->
				defer = $q.defer()
				if @isWebView()
					parsePlugin.getInstallationId (installationId)-> 
						defer.resolve installationId
					, (error) ->
						defer.reject error
				else
					defer.resolve 'DUMMY_INSTALLATION_ID'

				defer.promise







]

