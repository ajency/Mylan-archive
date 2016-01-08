angular.module('angularApp.questionnaire', []).controller('summaryController', [
  '$scope', 'QuestionAPI', '$routeParams', 'CToast', function($scope, QuestionAPI, $routeParams, CToast) {
    return $scope.view = {
      data: [],
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
            return CToast.show('submiteed successfully ');
          };
        })(this), (function(_this) {
          return function(error) {
            console.log('error');
            console.log(error);
            return CToast.show('Error in submitting questionnarie');
          };
        })(this))["finally"](function() {});
      }
    };
  }
]);
