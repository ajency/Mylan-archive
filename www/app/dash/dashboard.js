angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', 'HospitalData', 'NotifyCount', '$rootScope', function($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData, NotifyCount, $rootScope) {
    $scope.view = {
      hospitalName: HospitalData.name,
      projectName: HospitalData.project,
      SubmissionData: [],
      data: [],
      display: 'loader',
      infoMsg: null,
      limitTo: 5,
      showMoreButton: true,
      scroll: false,
      errorStartQuestion: false,
      errorMsg: '',
      showStart: false,
      currentDate: moment().format('MMMM Do YYYY'),
      onPullToRefresh: function() {
        this.showMoreButton = false;
        this.getSubmission();
        this.limitTo = 5;
        this.scroll = false;
        this.errorStartQuestion = false;
        return this.errorMsg = '';
      },
      init: function() {
        return Storage.getNextQuestion('set', 1);
      },
      startQuiz: function(val) {
        return App.navigate('start-questionnaire', {
          responseId: val
        });
      },
      getSubmission: function() {
        this.showMoreButton = false;
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
              if (arr.length < 6) {
                _this.showMoreButton = false;
              } else {
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
              if (!_.isEmpty(_.where(_this.data, {
                status: "late"
              }))) {
                arr.push(_.where(_this.data, {
                  status: "late"
                }));
              }
              if (arr.length === 0) {
                _this.infoMsg = true;
              } else {
                _this.infoMsg = false;
              }
              if (arr.length === 0) {
                _this.showStart = true;
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
        if (Storage.getQuestStatus('get', 'questionnarireError') === 'questionnarireError') {
          this.errorStartQuestion = true;
          this.errorMsg = 'An error occurred while starting questionnaire. Please try again';
        }
        if (Storage.getQuestStatus('get', 'questionnarireError') === 'offline') {
          this.errorStartQuestion = true;
          this.errorMsg = 'Unable to start questionnaire. Please check your internet connection.';
        }
        if (Storage.getQuestStatus('get', 'questionnarireError') === 'already_taken') {
          this.errorStartQuestion = true;
          this.errorMsg = 'The questionnaire has been already  started.';
        }
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
        this.getSubmission();
        this.errorStartQuestion = false;
        return this.errorMsg = '';
      },
      showMore: function() {
        this.limitTo = this.limitTo + 5;
        App.resize();
        if (this.data.length < this.limitTo) {
          this.showMoreButton = false;
        }
        if (this.limitTo >= 25) {
          return this.scroll = true;
        }
      },
      scrollTop: function() {
        App.scrollTop();
        return this.scroll = false;
      },
      getScrollPosition: function() {
        var scrollPosition;
        console.log('getscroll position');
        scrollPosition = App.getScrollPosition();
        console.log(scrollPosition);
        if (scrollPosition < 200) {
          return $scope.$apply(function() {
            return $scope.view.scroll = false;
          });
        } else if (scrollPosition > 1000) {
          if (this.limitTo >= 25) {
            return $scope.$apply(function() {
              return $scope.view.scroll = true;
            });
          }
        }
      },
      scrollButtom: function() {
        return App.scrollBottom();
      }
    };
    $scope.$on('$ionicView.enter', function(event, viewData) {
      console.log('view enter');
      $scope.view.displaydata();
      return Storage.setData('refcode', 'get').then(function(refcode) {
        return NotifyCount.getCount(refcode);
      });
    });
    $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      $scope.view.display = 'loader';
      $scope.view.data = [];
      $scope.view.limitTo = 5;
      $scope.view.showMoreButton = false;
      $scope.view.scroll = false;
      $scope.view.errorStartQuestion = false;
      return $scope.view.showStart = false;
    });
    return $rootScope.$on('in:app:notification', function(e, obj) {
      App.scrollTop();
      return $scope.view.onPullToRefresh();
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
