angular.module('PatientApp.dashboard').factory('DashboardAPI', [
  '$q', '$http', 'App', '$stateParams', function($q, $http, App, $stateParams) {
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
