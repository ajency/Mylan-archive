angular.module 'angularApp.dashboard'

.controller 'StartQuestionnaireCtrl', ['$scope', 'QuestionAPI', '$routeParams', '$location', 'Storage'
	, ($scope, QuestionAPI, $routeParams, $location, Storage)->

		$scope.view =
			startQuiz :(quizID) ->
				questionnaireData = 
					respStatus : 'noValue'
					responseId : ''

				Storage.questionnaire 'set', questionnaireData
				$location.path 'questionnaire'
				

			init :() ->
				startQuestionData = Storage.startQuestionnaire 'get'
				console.log 'start questinnarie...'
				if _.isEmpty startQuestionData 
					$location.path 'dashboard'




]