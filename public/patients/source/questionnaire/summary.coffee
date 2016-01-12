angular.module 'angularApp.questionnaire',[]

.controller 'summaryController', ['$scope', 'QuestionAPI', '$routeParams', 'CToast', '$location'
	, ($scope, QuestionAPI, $routeParams, CToast, $location)->

		$scope.view =
			data : []
			display : 'loader'

			init :() -> 

				param = 
					responseId : $routeParams.responseId
					
				QuestionAPI.getSummary(param)
				.then (data)=>
					@data = data
					@data.submissionDate = moment(@data.submissionDate).format('MMMM Do YYYY')
					@display = 'noError'
				,(error)=>
					@display = 'error'
					@errorType = error

			submitSummary : ->
				# CSpinner.show '', 'Please wait..'
				param = 
					responseId : $routeParams.responseId
				QuestionAPI.submitSummary param
				.then (data)=>
					CToast.show 'submiteed successfully '
					# App.navigate 'exit-questionnaire'
				,(error)=>
					console.log 'error'
					console.log error
					CToast.show 'Error in submitting questionnarie'
				.finally ->
					# CSpinner.hide()

			back :->
				# deregister()
				# if App.previousState == 'dashboard'
				# 	App.navigate 'dashboard'

				$location.path 'questionnaire/lastQuestion/'+$routeParams.responseId


			onTapToRetry : ->
				@display = 'loader'
				@init()
				
				



]


