angular.module 'PatientApp.Quest'

.controller 'SummaryCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'Storage', 'CToast', 'CSpinner', '$ionicPlatform'
	, ($scope, App, QuestionAPI, $stateParams, Storage, CToast, CSpinner, $ionicPlatform)->

		$scope.view =
			title: 'C-weight'
			data : []
			go : ''
			response : ''
			display : 'loader'
			hideButton : ''

			getSummaryApi :()->
				@hideButton = if App.previousState == 'dashboard' then true else false
				param =
						'responseId' : $stateParams.summary
				@display = 'loader'
				QuestionAPI.getSummary param
				.then (data)=>
					console.log '****name***'
					console.log data
					@data = data
					_.each @data, (value)->
						a = value.input
						if !_.isUndefined a
							value['type'] = 'input'
						else
							value['type'] = 'option'

					@display = 'noError'
				,(error)=>
					@display = 'error'
					@errorType = error
					
			init : ->
				@getSummaryApi()

			submitSummary : ->

				CToast.showLongBottom 'questionnaire not submitted, its a dummy app.'
				App.navigate 'exit-questionnaire'
				
				# CSpinner.show '', 'Please wait..'

				# param = 
				# 	responseId : $stateParams.summary
				# QuestionAPI.submitSummary param
				# .then (data)=>
				# 	CToast.show 'Successfully submitted'
				# 	App.navigate 'exit-questionnaire'
				# 	# deregister()
				# ,(error)=>
				# 	if error == 'offline'
				# 		CToast.showLongBottom 'Please check your internet connection'
				# 	else if error == 'server_error'
				# 		CToast.showLongBottom 'Error in submitting questionnaire,Server error'
				# 	else
				# 		CToast.showLongBottom 'Error in submitting questionnaire,try again'
				# .finally ->
				# 	CSpinner.hide()

			prevQuestion : ->
				valueAction = QuestionAPI.setAction 'get'
				action =
					questionId : valueAction.questionId
					mode : 'prev'
				QuestionAPI.setAction 'set', action
				App.navigate 'questionnaire', quizID: $stateParams.quizID

			onTapToRetry : ->
				@display = 'loader'
				@getSummaryApi()

			back :->
				# deregister()
				if App.previousState == 'dashboard'
					App.navigate 'dashboard'
				else
					Storage.setData 'responseId', 'set', $stateParams.summary 
					.then ()->
						App.navigate 'questionnaire', respStatus:'lastQuestion'

		onDeviceBackSummary = ->
			$scope.view.back()

		deregister = null	
		$scope.$on '$ionicView.enter', ->
			console.log '$ionicView.enter.summary'
			#Device hardware back button for android
			deregister = $ionicPlatform.registerBackButtonAction onDeviceBackSummary, 1000
			# $ionicPlatform.onHardwareBackButton onDeviceBackSummary
		
		$scope.$on '$ionicView.leave', ->
			console.log '$ionicView.enter.leave summary'
			if deregister then deregister()
			# $ionicPlatform.offHardwareBackButton onDeviceBackSummary

]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'summary',
			url: '/summary:summary'
			parent: 'main'
			cache: false
			views: 
				"appContent":
					templateUrl: 'views/questionnaire/summary.html'
					controller: 'SummaryCtr'
]
