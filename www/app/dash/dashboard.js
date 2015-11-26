(function() {
  angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
    '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', 'HospitalData', function($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData) {
      return $scope.view = {
        hospitalName: HospitalData.name,
        projectName: HospitalData.project,
        SubmissionData: [],
        init: function() {
          return Storage.getNextQuestion('set', 1);
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
            controller: 'DashboardCtrl',
            resolve: {
              HospitalData: function($q, Storage) {
                var defer;
                defer = $q.defer();
                Storage.hospital_data('get').then(function(data) {
                  return defer.resolve(data);
                });
                return defer.promise;
              }
            }
          }
        }
      });
    }
  ]);

}).call(this);
