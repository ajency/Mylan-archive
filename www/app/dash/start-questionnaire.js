angular.module('PatientApp.dashboard').controller('StartQuestionnaireCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', function($scope, App, Storage, QuestionAPI, DashboardAPI) {
    return $scope.view = {
      startQuiz: function(quizID) {
        return App.navigate('questionnaire', {
          respStatus: 'noValue'
        });
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('start-questionnaire', {
      url: '/start-questionnaire',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/dashboard/start-questionnaire.html',
          controller: 'StartQuestionnaireCtrl'
        }
      }
    });
  }
]);
