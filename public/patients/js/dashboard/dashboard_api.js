angular.module('angularApp.dashboard').factory('DashboardAPI', [
  '$q', '$http', function($q, $http) {
    var DashboardAPI;
    DashboardAPI = {};
    DashboardAPI.get = function(id) {
      var PARSE_HEADERS, PARSE_URL, defer, param, url;
      defer = $q.defer();
      PARSE_URL = 'https://api.parse.com/1/functions';
      url = PARSE_URL + '/dashboard';
      PARSE_HEADERS = {
        headers: {
          "X-Parse-Application-Id": 'MQiH2NRh0G6dG51fLaVbM0i7TnxqX2R1pKs5DLPA',
          "X-Parse-REST-API-KeY": 'I4yEHhjBd4e9x28MvmmEOiP7CzHCVXpJxHSu5Xva'
        }
      };
      param = {
        "patientId": id
      };
      $http.post(url, param, PARSE_HEADERS).then(function(data) {
        console.log('dashboard data ');
        console.log(data);
        return defer.resolve(data.data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(_this.errorCode(error));
        };
      })(this));
      return defer.promise;
    };
    return DashboardAPI;
  }
]);
