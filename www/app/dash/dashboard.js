angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', 'HospitalData', function($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData) {
    return $scope.view = {
      hospitalName: HospitalData.name,
      projectName: HospitalData.project,
      SubmissionData: [],
      data: [],
      display: 'loader',
      init: function() {
        return Storage.getNextQuestion('set', 1);
      },
      startQuiz: function() {
        return App.navigate('start-questionnaire');
      },
      getSubmission: function() {
        this.display = 'loader';
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            var param;
            param = {
              "patientId": refcode
            };
            return DashboardAPI.get(param).then(function(data) {
              console.log('inside then');
              console.log(data);
              _this.data = data;
              return _this.display = 'noError';
            }, function(error) {
              _this.display = 'error';
              return _this.errorType = error;
            });
          };
        })(this));
      },
      displaydata: function() {
        return this.getSubmission();
      },
      summary: function(id) {
        console.log('---summary---id');
        console.log(id);
        return App.navigate('summary', {
          summary: id
        });
      },
      resumeQuiz: function(id) {
        console.log('resumeQuiz');
        console.log(id);
        return App.navigate('questionnaire', {
          respStatus: id
        });
      },
      onTapToRetry: function() {
        this.display = 'loader';
        console.log('onTapToRetry');
        return this.getSubmission();
      },
      pastDate: function(date) {
        return moment(date).format('MMMM Do YYYY');
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
