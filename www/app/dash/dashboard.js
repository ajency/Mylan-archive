angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', 'HospitalData', 'NotifyCount', function($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData, NotifyCount) {
    $scope.view = {
      hospitalName: HospitalData.name,
      projectName: HospitalData.project,
      SubmissionData: [],
      data: [],
      display: 'loader',
      infoMsg: null,
      limitTo: 5,
      showMoreButton: true,
      onPullToRefresh: function() {
        this.showMoreButton = true;
        this.data = [];
        this.getSubmission();
        return this.limitTo = 5;
      },
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
              arr = _.reject(_this.data, function(d) {
                return d.status === 'base_line';
              });
              if (arr.length <= 6) {
                _this.showMoreButton = true;
              }
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
            })["finally"](function() {
              $scope.$broadcast('scroll.refreshComplete');
              return App.resize();
            });
          };
        })(this));
      },
      displaydata: function() {
        this.data = [];
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
      },
      showMore: function() {
        this.limitTo = this.limitTo + 5;
        App.resize();
        if (this.data.length < this.limitTo) {
          return this.showMoreButton = false;
        }
      }
    };
    return $scope.$on('$ionicView.enter', function(event, viewData) {
      console.log('view enter');
      $scope.view.displaydata();
      return Storage.setData('refcode', 'get').then(function(refcode) {
        return NotifyCount.getCount(refcode);
      });
    });
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
          cache: false,
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
