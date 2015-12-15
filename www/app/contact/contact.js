angular.module('PatientApp.contact', []).controller('contactCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', function($scope, App, Storage, QuestionAPI, DashboardAPI) {
    return $scope.view = {
      startQuiz: function(quizID) {
        return App.navigate('questionnaire');
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('contact', {
      url: '/contact',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/contact/contact.html',
          controller: 'contactCtrl'
        }
      }
    });
  }
]);
