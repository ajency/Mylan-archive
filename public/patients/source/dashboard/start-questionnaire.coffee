angular.module 'angularApp.dashboard'

.controller 'StartQuestionnaireCtrl', ['$scope', 'QuestionAPI', '$routeParams', '$location', 'Storage'
	, ($scope, QuestionAPI, $routeParams, $location, Storage)->

		$scope.view =
			email : hospitalEmail
			phone : hospitalPhone
			projectName : questionnaireName
			
			startQuiz :(quizID) ->

				value = Storage.startQuestionnaire 'get'
				if value == 'noValue'

					questionnaireData = 
						respStatus : 'noValue'
						responseId : ''
				else
					questionnaireData = 
						respStatus : 'resume'
						responseId : value

				Storage.questionnaire 'set', questionnaireData
				$location.path 'questionnaire'
				

			init :() ->
				startQuestionData = Storage.startQuestionnaire 'get'
				console.log 'start questinnarie...'
				if _.isEmpty startQuestionData 
					$location.path 'dashboard'




]