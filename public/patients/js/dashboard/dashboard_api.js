angular.module('angularApp.dashboard').factory('DashboardAPI', [
  '$q', '$http', 'App', function($q, $http, App) {
    var DashboardAPI;
    DashboardAPI = {};
    DashboardAPI.get = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('dashboard', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    return DashboardAPI;
  }
]);
