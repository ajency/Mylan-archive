angular.module 'PatientApp.Global', []


.factory 'App', [ '$state', '$ionicHistory', '$window', '$q', '$http', '$cordovaNetwork'
	, '$cordovaPreferences', '$ionicScrollDelegate', '$cordovaKeyboard'
	,( $state, $ionicHistory, $window, $q, $http, $cordovaNetwork, $cordovaPreferences, $ionicScrollDelegate, $cordovaKeyboard)->

		App = 
			start: true
			validateEmail: /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/
			onlyNumbers: /^\d+$/
			menuEnabled : left: false, right: false
			# ContactUsEnabled : true
			# UpdatesEnabled: true
			# resetPassword : true
			previousState: ''
			currentState: ''
			questinnarieButton : ''
			menuClass : true

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

			disableNativeScroll : ->
				console.log 'disable native Scroll'
				# if $window.cordova && $window.cordova.plugins.Keyboard
				# 	$cordovaKeyboard.disableScroll true

			errorCode : (error) ->
				console.log error
				
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

			scrollTop : ->
				$ionicScrollDelegate.scrollTop true

			scrollBottom : ->
				$ionicScrollDelegate.scrollBottom true

			getScrollPosition : ->
				$ionicScrollDelegate.getScrollPosition().top

			parseErrorCode :(error)->
				errType = ''
				errMsg = error.message
				if error.code == 100
					errType = 'offline'
				else if error.code == 141
					errType = 'server_error'
				else if errMsg.code == 101
					errType = 'server_error'
				else if errMsg.code == 124
					errType = 'offline'
				errType

			SendParseRequest :(cloudFun, param)->
				defer = $q.defer()
				Parse.Cloud.run cloudFun, param,	
					success: (result) ->
						defer.resolve result
					error: (error) =>
						console.log 'inside error common function'
						console.log error
						defer.reject @parseErrorCode error
				defer.promise

			callUs :(tel) ->
				console.log 'call us'
				console.log tel
				document.location.href = "tel:"+tel







]

