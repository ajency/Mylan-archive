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
      url = Url + '/api/v1/user/setpassword';
      AUTH_HEADERS = {
        headers: {
          "X-API-KEY": APP_KEY,
          "X-Authorization": APP_AuthrizationKey,
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
