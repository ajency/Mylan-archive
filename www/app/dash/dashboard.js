angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', function($scope, App, Storage, QuestionAPI, DashboardAPI) {
    return $scope.view = {
      hospitalName: '',
      projectName: '',
      SubmissionData: [],
      init: function() {
        var value;
        Storage.getNextQuestion('set', 1);
        value = Storage.setHospitalData('get');
        this.hospitalName = value['name'];
        return this.projectName = value['project'];
      },
      startQuiz: function(quizID) {
        Storage.quizDetails('set', {
          quizID: quizID
        });
        return App.navigate('questionnaire', {
          quizID: quizID
        });
      },
      getSubmission: function() {
        return DashboardAPI.get();
      },
      displaydata: function() {
        this.data = this.getSubmission();
        return console.log(this.data);
      },
      summary: function() {
        return App.navigate('summary', {
          quizID: 111
        });
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
