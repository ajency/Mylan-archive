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

				CSpinner.show '', 'Please wait..'

				param = 
					responseId : $stateParams.summary
				QuestionAPI.submitSummary param
				.then (data)=>
					CToast.show 'Submitted Successfully'
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
					Storage.setData 'responseId', 'set', $stateParams.summary 
					.then ()->
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
