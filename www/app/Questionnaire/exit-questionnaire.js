angular.module('PatientApp.Quest').controller('ExitQuestionnaireCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', function($scope, App, Storage, QuestionAPI, DashboardAPI) {
    return $scope.view = {
      exit: function() {
        return ionic.Platform.exitApp();
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('exit-questionnaire', {
      url: '/exit-questionnaire',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/questionnaire/exit.html',
          controller: 'ExitQuestionnaireCtrl'
        }
      }
    });
  }
]);
