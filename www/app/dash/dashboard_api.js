angular.module('PatientApp.dashboard').factory('DashboardAPI', [
  '$q', '$http', 'App', '$stateParams', function($q, $http, App, $stateParams) {
    var DashboardAPI;
    DashboardAPI = {};
    DashboardAPI.get = function(param) {
      var defer, url;
      defer = $q.defer();
      url = PARSE_URL + '/dashboard';
      App.sendRequest(url, param, PARSE_HEADERS).then(function(data) {
        return defer.resolve(data.data);
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
