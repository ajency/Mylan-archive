angular.module('PatientApp.notification', []).controller('contactCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', function($scope, App, Storage, QuestionAPI, DashboardAPI) {
    return $scope.view = {
      startQuiz: function(quizID) {
        return App.navigate('questionnaire');
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('notification', {
      url: '/notification',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/notification/notification.html',
          controller: 'contactCtrl'
        }
      }
    });
  }
]);
