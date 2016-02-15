angular.module('angularApp.dashboard', []).controller('dashboardController', [
  '$scope', 'DashboardAPI', '$location', 'Storage', function($scope, DashboardAPI, $location, Storage) {
    return $scope.view = {
      data: [],
      display: 'loader',
      QuestinnarieName: questionnaireName,
      showMoreButton: true,
      limitTo: 5,
      init: function() {
        var id, param;
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
  '$scope', '$location', function($scope, $location) {
    console.log('header ctrl');
    return $scope.notifyIocn = '23';
  }
]);
