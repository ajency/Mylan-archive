angular.module('angularApp.dashboard', []).controller('dashboardController', [
  '$scope', 'DashboardAPI', '$location', function($scope, DashboardAPI, $location) {
    return $scope.view = {
      data: [],
      display: 'loader',
      QuestinnarieName: questionnaireName,
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
            return _this.display = 'noError';
          };
        })(this), (function(_this) {
          return function(error) {
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this));
      },
      summary: function(id) {
        return $location.path('summary/' + id);
      },
      startQuiz: function() {
        return $location.path('start-questionnaire');
      },
      resumeQuiz: function(id) {
        return $location.path('questionnaire/' + id + '/000');
      },
      onTapToRetry: function() {
        this.display = 'loader';
        console.log('onTapToRetry');
        return this.init();
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
  '$scope', function($scope) {
    console.log('header ctrl');
    return $scope.view = {
      notifyClick: function() {
        return console.log('ntoification clciked');
      }
    };
  }
]);
