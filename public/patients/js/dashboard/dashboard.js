angular.module('angularApp.dashboard', []).controller('dashboardController', [
  '$scope', 'DashboardAPI', '$location', 'Storage', '$rootScope', function($scope, DashboardAPI, $location, Storage, $rootScope) {
    $scope.view = {
      data: [],
      display: 'loader',
      QuestinnarieName: questionnaireName,
      showMoreButton: false,
      limitTo: 10,
      errorStartQuestion: false,
      phone: hospitalPhone,
      errorMsg: '',
      email: hospitalEmail,
      init: function() {
        var id, param, questionnaireData, startQuestData, summaryData;
        if (Storage.getQuestStatus('get', 'questionnarireError') === 'questionnarireError') {
          this.errorStartQuestion = true;
          this.errorMsg = 'An error occurred while starting questionnaire. Please try again';
        } else if (Storage.getQuestStatus('get', 'questionnarireError') === 'offline') {
          this.errorStartQuestion = true;
          this.errorMsg = 'Unable to start questionnaire. Please check your internet connection.';
        } else if (Storage.getQuestStatus('get', 'questionnarireError') === 'already_taken') {
          this.errorStartQuestion = true;
          this.errorMsg = 'The questionnaire has been already  started.';
        } else if (Storage.getQuestStatus('get', 'questionnarireError') !== '') {
          this.errorStartQuestion = true;
          this.errorMsg = Storage.getQuestStatus('get', 'questionnarireError');
        }
        questionnaireData = {};
        Storage.questionnaire('set', questionnaireData);
        startQuestData = {};
        Storage.startQuestionnaire('set', startQuestData);
        summaryData = {};
        Storage.summary('set', summaryData);
        this.display = 'loader';
        id = RefCode;
        param = {
          "patientId": id
        };
        return DashboardAPI.get(param).then((function(_this) {
          return function(data) {
            var arr;
            _this.data = data;
            _this.display = 'noError';
            arr = _.reject(_this.data, function(d) {
              return d.status === 'base_line';
            });
            if (arr.length < 6) {
              return _this.showMoreButton = false;
            } else {
              return _this.showMoreButton = true;
            }
          };
        })(this), (function(_this) {
          return function(error) {
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this));
      },
      summary: function(id) {
        var summaryData;
        summaryData = {
          previousState: 'dashboard',
          responseId: id
        };
        Storage.summary('set', summaryData);
        return $location.path('summary');
      },
      startQuiz: function(val) {
        var startQuestData;
        startQuestData = val;
        Storage.startQuestionnaire('set', startQuestData);
        return $location.path('start-questionnaire');
      },
      resumeQuiz: function(id) {
        var questionnaireData;
        questionnaireData = {
          respStatus: 'resume',
          responseId: id
        };
        Storage.questionnaire('set', questionnaireData);
        return $location.path('questionnaire');
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.init();
      },
      showMore: function() {
        this.limitTo = this.limitTo + 5;
        if (this.data.length < this.limitTo) {
          return this.showMoreButton = false;
        }
      }
    };
    return $rootScope.$on('on:session:expiry', function() {
      Parse.User.logOut();
      return window.location = Url + "/auth/logout";
    });
  }
]).controller('EachRequestTimeCtrl', [
  '$scope', function($scope) {
    var setTime;
    setTime = function() {
      $scope.submissions.yr = moment($scope.submissions.occurrenceDate).format('YYYY');
      $scope.submissions.month = moment($scope.submissions.occurrenceDate).format('MMM');
      return $scope.submissions.date = moment($scope.submissions.occurrenceDate).format('Do');
    };
    return setTime();
  }
]).controller('headerCtrl', [
  '$scope', '$location', 'App', 'notifyAPI', '$rootScope', function($scope, $location, App, notifyAPI, $rootScope) {
    $scope.view = {
      notificationCount: 0,
      badge: false,
      getNotificationCount: function() {
        var param;
        param = {
          "patientId": RefCode
        };
        return notifyAPI.getNotificationCount(param).then((function(_this) {
          return function(data) {
            if (data > 0) {
              _this.notificationCount = data;
              return _this.badge = true;
            } else {
              _this.notificationCount = 0;
              return _this.badge = false;
            }
          };
        })(this));
      },
      decrement: function() {
        this.notificationCount = this.notificationCount - 1;
        if (this.notificationCount <= 0) {
          return this.badge = false;
        }
      },
      init: function() {
        return this.getNotificationCount();
      },
      deleteAllNotification: function() {
        this.notificationCount = 0;
        return this.badge = false;
      }
    };
    $rootScope.$on('notification:count', function() {
      return $scope.view.getNotificationCount();
    });
    $rootScope.$on('delete:all:count', function() {
      return $scope.view.deleteAllNotification();
    });
    return $rootScope.$on('decrement:notification:count', function() {
      return $scope.view.decrement();
    });
  }
]);
