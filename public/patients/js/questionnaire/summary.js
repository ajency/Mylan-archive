angular.module('angularApp.questionnaire', []).controller('summaryController', [
  '$scope', 'QuestionAPI', '$routeParams', 'CToast', '$location', 'App', function($scope, QuestionAPI, $routeParams, CToast, $location, App) {
    return $scope.view = {
      data: [],
      display: 'loader',
      init: function() {
        var param;
        param = {
          responseId: $routeParams.responseId
        };
        return QuestionAPI.getSummary(param).then((function(_this) {
          return function(data) {
            _this.data = data;
            _this.data.submissionDate = moment(_this.data.submissionDate).format('MMMM Do YYYY');
            return _this.display = 'noError';
          };
        })(this), (function(_this) {
          return function(error) {
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this));
      },
      submitSummary: function() {
        var param;
        param = {
          responseId: $routeParams.responseId
        };
        return QuestionAPI.submitSummary(param).then((function(_this) {
          return function(data) {
            CToast.show('submiteed successfully ');
            return $location.path('dashboard');
          };
        })(this), (function(_this) {
          return function(error) {
            console.log('error');
            console.log(error);
            return CToast.show('Error in submitting questionnarie');
          };
        })(this))["finally"](function() {});
      },
      back: function() {
        if (App.previousState === 'dashboardController') {
          return $location.path('dashboard');
        } else {
          return $location.path('questionnaire/lastQuestion/' + $routeParams.responseId);
        }
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.init();
      }
    };
  }
]);
