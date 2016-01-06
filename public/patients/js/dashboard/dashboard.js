angular.module('angularApp.dashboard', []).controller('dashboardController', [
  '$scope', 'DashboardAPI', '$location', function($scope, DashboardAPI, $location) {
    return $scope.view = {
      data: [],
      init: function() {
        var id;
        console.log('inside inita2323');
        console.log(refernceCode);
        id = refernceCode;
        return DashboardAPI.get(id).then((function(_this) {
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
      summary: function(id) {
        return $location.path('summary/' + id);
      }
    };
  }
]).controller('EachRequestTimeCtrl', [
  '$scope', function($scope) {
    var setTime;
    setTime = function() {
      return $scope.submissions.date = moment($scope.submissions.occurrenceDate.iso).format('MMMM Do YYYY');
    };
    return setTime();
  }
]);
