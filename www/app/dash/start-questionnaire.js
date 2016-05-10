angular.module('PatientApp.dashboard').controller('StartQuestionnaireCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', '$stateParams', function($scope, App, Storage, QuestionAPI, DashboardAPI, $stateParams) {
    return $scope.view = {
      startQuiz: function() {
        return App.navigate('questionnaire', {
          respStatus: $stateParams.responseId
        });
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('start-questionnaire', {
      url: '/start-questionnaire:responseId',
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
