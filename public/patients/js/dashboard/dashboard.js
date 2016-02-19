angular.module('angularApp.dashboard', []).controller('dashboardController', [
  '$scope', 'DashboardAPI', '$location', 'Storage', function($scope, DashboardAPI, $location, Storage) {
    return $scope.view = {
      data: [],
      display: 'loader',
      QuestinnarieName: questionnaireName,
      showMoreButton: true,
      limitTo: 5,
      init: function() {
        var id, param, questionnaireData, startQuestData, summaryData;
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
            _this.data = data;
            _this.display = 'noError';
            if (_this.data.length < 6) {
              return _this.showMoreButton = false;
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
      startQuiz: function() {
        var startQuestData;
        startQuestData = 'start';
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
        console.log('onTapToRetry');
        return this.init();
      },
      showMore: function() {
        this.limitTo = this.limitTo + 5;
        if (this.data.length < this.limitTo) {
          return this.showMoreButton = false;
        }
      }
    };
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
        console.log('inside getNotificationCount');
        param = {
          "patientId": RefCode
        };
        return notifyAPI.getNotificationCount(param).then((function(_this) {
          return function(data) {
            if (data > 0) {
              _this.notificationCount = data;
              return _this.badge = true;
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
        console.log('init');
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
