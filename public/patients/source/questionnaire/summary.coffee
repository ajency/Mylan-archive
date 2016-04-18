angular.module 'angularApp.questionnaire',[]

.controller 'summaryController', ['$scope', 'QuestionAPI', '$routeParams', 'CToast', '$location', 'App', 'Storage'
	, ($scope, QuestionAPI, $routeParams, CToast, $location, App, Storage)->

		$scope.view =
			data : []
			display : 'loader'
			hideButton : null
			responseId : ''

			init :() -> 
				console.log 'summaryyyy'
				summaryData = Storage.summary 'get'
				console.log summaryData

				if !_.isEmpty(summaryData) 

					@responseId = summaryData.responseId

					if summaryData.previousState == 'questionnaire'
						
						questionnaireData = 
							respStatus : 'lastQuestion'
							responseId : @responseId

						Storage.questionnaire 'set', questionnaireData


					@hideButton = if summaryData.previousState == 'questionnaire' then true else false
					# @hideButton = if App.previousState != 'questionnaireCtr' then false else true
					console.log 'hide'
					console.log @hideButton

					param = 
						responseId : @responseId
						
					QuestionAPI.getSummary(param)
					.then (data)=>
						@data = data
						@data.submissionDate = moment(@data.submissionDate).format('MMMM Do YYYY')
						
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
				else
					$location.path 'dashboard'

			submitSummary : ->
				$('#submitSummaryModal').modal('hide')
				$('.modal-backdrop').addClass('hidden')
				# CSpinner.show '', 'Please wait..'
				param = 
					responseId : @responseId
				QuestionAPI.submitSummary param
				.then (data)=>
					CToast.show 'submiteed successfully '
					questionnaireData = {}
					Storage.questionnaire 'set', questionnaireData
					$location.path 'dashboard'
				,(error)=>
					console.log 'error'
					console.log error
					CToast.show 'Error in submitting questionnarie'
				.finally ->
					# CSpinner.hide()

			back :->
				if @hideButton == false
					$location.path 'dashboard'
				else	
					questionnaireData = 
						respStatus : 'lastQuestion'
						responseId : @responseId

					Storage.questionnaire 'set', questionnaireData

					$location.path 'questionnaire'

			onTapToRetry : ->
				@display = 'loader'
				@init()

			goToFirstQuestion : ->
				$('#submitSummaryModal').modal('hide')
				$('.modal-backdrop').addClass('hidden')
				questionnaireData = 
						respStatus : 'firstQuestion'
						responseId : @responseId

					Storage.questionnaire 'set', questionnaireData

					$location.path 'questionnaire'

			onSumbmit : ->
					if @data.editable == true 
						$('#submitSummaryModal').modal('show')
						return
															
					else
						@submitSummary()

				
				



]


