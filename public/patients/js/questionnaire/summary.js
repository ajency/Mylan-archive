angular.module('angularApp.questionnaire', []).controller('summaryController', [
  '$scope', 'QuestionAPI', '$routeParams', function($scope, QuestionAPI, $routeParams) {
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
      }
    };
  }
]);
