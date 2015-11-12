angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', function($scope, App, Storage, QuestionAPI, DashboardAPI) {
    return $scope.view = {
      Hospital_name: 'Sutter Davis Hospital',
      SubmissionData: [],
      startQuiz: function() {
        Storage.quizDetails('set', {
          quizID: '1111'
        });
        return App.navigate('questionnaire', {
          quizID: '1111'
        });
      },
      getSubmission: function() {
        return DashboardAPI.get();
      },
      displaydata: function() {
        this.data = this.getSubmission();
        return console.log(this.data);
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('dashboard', {
      url: '/dashboard',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/dashboard/dashboard.html',
          controller: 'DashboardCtrl'
        }
      }
    });
  }
]);
