angular.module 'PatientApp.Quest'

.controller 'SummaryCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'Storage'
	, ($scope, App, QuestionAPI, $stateParams, Storage)->

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
				param = 
					responseId : @responseId
				QuestionAPI.submitSummary param
				.then (data)=>
					console.log 'data'
					console.log 'succ submiteed'
				,(error)=>
					console.log 'error'
					console.log error

					
				ionic.Platform.exitApp()

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
