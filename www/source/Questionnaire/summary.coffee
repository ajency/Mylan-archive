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

			getSummary : ->
				@display = 'noError'
				@summary = Storage.summary('get')
				console.log '---summary---'
				console.log @summary
				@data = @summary.summary
				@responseId = @summary.responseId

			getSummaryApi :()->
				param =
						'responseId' : $stateParams.summary
				@display = 'loader'
				QuestionAPI.getSummary param
				.then (data)=>
					console.log '--getSummaryApi---'
					@data = data.result
					console.log @data
					@display = 'noError'
				,(error)=>
					@display = 'error'
					@errorType = error
					
			init : ->
				@summarytype = $stateParams.summary
				if @summarytype == 'set'
					@getSummary()
				else 
					@getSummaryApi()

			submitSummary : ->

				CSpinner.show '', 'Please wait..'

				param = 
					responseId : $stateParams.summary
				QuestionAPI.submitSummary param
				.then (data)=>
					console.log 'data'
					console.log 'succ submiteed'
					CToast.show 'submiteed successfully '
					App.navigate 'exit-questionnaire'
					deregister()
				,(error)=>
					console.log 'error'
					console.log error
					CToast.show 'Error in submitting questionnarie'
				.finally ->
					CSpinner.hide()

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
				deregister()
				if App.previousState == 'dashboard'
					App.navigate 'dashboard'
				else
					App.navigate 'questionnaire', respStatus:'lastQuestion'

		onDeviceBack = ->
			$scope.view.back()

		deregister = null	
		$scope.$on '$ionicView.afterEnter', ->
			#Device hardware back button for android
			deregister = $ionicPlatform.registerBackButtonAction onDeviceBack, 1000
		
		$scope.$on '$ionicView.leave', ->
			$ionicPlatform.offHardwareBackButton onDeviceBack

]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'summary',
			url: '/summary:summary'
			parent: 'parent-questionnaire'
			views: 
				"QuestionContent":
					templateUrl: 'views/questionnaire/summary.html'
					controller: 'SummaryCtr'
]
