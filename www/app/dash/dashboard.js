angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', 'HospitalData', function($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData) {
    return $scope.view = {
      hospitalName: HospitalData.name,
      projectName: HospitalData.project,
      SubmissionData: [],
      data: [],
      display: 'loader',
      infoMsg: null,
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
              var arr;
              console.log('dashoard data');
              console.log(data);
              _this.data = data;
              arr = [];
              if (!_.isEmpty(_.where(_this.data, {
                status: "due"
              }))) {
                arr.push(_.where(_this.data, {
                  status: "due"
                }));
              }
              if (!_.isEmpty(_.where(_this.data, {
                status: "started"
              }))) {
                arr.push(_.where(_this.data, {
                  status: "started"
                }));
              }
              if (arr.length === 0) {
                _this.infoMsg = true;
              } else {
                _this.infoMsg = false;
              }
              _.each(_this.data, function(value) {
                return value.occurrenceDate = moment(value.occurrenceDate).format('MMMM Do YYYY');
              });
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
        return App.navigate('summary', {
          summary: id
        });
      },
      resumeQuiz: function(id) {
        return App.navigate('questionnaire', {
          respStatus: id
        });
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.getSubmission();
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
