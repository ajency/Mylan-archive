angular.module('angularApp.Auth').factory('AuthAPI', [
  '$q', 'App', '$http', function($q, App, $http) {
    var AuthAPI;
    AuthAPI = {};
    AuthAPI.setPassword = function(refrencecode, password) {
      var AUTH_HEADERS, defer, params, url;
      params = {
        "referenceCode": refrencecode,
        "password": password
      };
      defer = $q.defer();
      url = 'http://mylantest.ajency.in/api/v1/user/setpassword';
      AUTH_HEADERS = {
        headers: {
          "X-API-KEY": 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y',
          "X-Authorization": 'e7968bf3f5228312f344339f3f9eb19701fb7a3c',
          "Content-Type": 'application/json'
        }
      };
      App.sendRequest(url, params, AUTH_HEADERS).then(function(data) {
        return defer.resolve(data.data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    return AuthAPI;
  }
]);
