angular.module('angularApp.dashboard').controller('StartQuestionnaireCtrl', [
  '$scope', 'QuestionAPI', '$routeParams', '$location', 'Storage', function($scope, QuestionAPI, $routeParams, $location, Storage) {
    return $scope.view = {
      startQuiz: function(quizID) {
        var questionnaireData;
        questionnaireData = {
          respStatus: 'noValue',
          responseId: ''
        };
        Storage.questionnaire('set', questionnaireData);
        return $location.path('questionnaire');
      },
      init: function() {
        var startQuestionData;
        startQuestionData = Storage.startQuestionnaire('get');
        console.log('start questinnarie...');
        if (_.isEmpty(startQuestionData)) {
          return $location.path('dashboard');
        }
      }
    };
  }
]);
