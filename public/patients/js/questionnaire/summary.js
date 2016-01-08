angular.module('angularApp.questionnaire', []).controller('summaryController', [
  '$scope', 'QuestionAPI', '$routeParams', 'CToast', function($scope, QuestionAPI, $routeParams, CToast) {
    return $scope.view = {
      data: [],
      init: function() {
        var id;
        id = $routeParams.responseId;
        console.log('******');
        console.log(id);
        console.log('inside init');
        return QuestionAPI.getSummary(id).then((function(_this) {
          return function(data) {
            _this.data = data.result;
            console.log('inside then');
            console.log(_this.data);
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
            console.log('data');
            console.log('succ submiteed');
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
