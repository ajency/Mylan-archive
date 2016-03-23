angular.module('angularApp.dashboard').controller('StartQuestionnaireCtrl', [
  '$scope', 'QuestionAPI', '$routeParams', '$location', 'Storage', function($scope, QuestionAPI, $routeParams, $location, Storage) {
    return $scope.view = {
      email: hospitalEmail,
      phone: hospitalPhone,
      startQuiz: function(quizID) {
        var questionnaireData, value;
        value = Storage.startQuestionnaire('get');
        if (value === 'noValue') {
          questionnaireData = {
            respStatus: 'noValue',
            responseId: ''
          };
        } else {
          questionnaireData = {
            respStatus: 'resume',
            responseId: value
          };
        }
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
