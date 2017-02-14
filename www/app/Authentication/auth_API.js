(function() {
  angular.module('PatientApp.Auth').factory('AuthAPI', [
    '$q', 'App', '$http', 'UrlList', 'Push', function($q, App, $http, UrlList, Push) {
      var AuthAPI;
      AuthAPI = {};
      AuthAPI.validateRefCode = function(refcode, deviceUUID, deviceOS, Push) {
        var defer, params, url;
        params = {
          "referenceCode": refcode,
          "deviceType": 'mobile',
          "deviceIdentifier": deviceUUID,
          "deviceOS": deviceOS,
          "accessType": 'app'
        };
        defer = $q.defer();
        url = AUTH_URL + '/user/dosetup';
        App.sendRequest(url, params, AUTH_HEADERS).then(function(data) {
          return defer.resolve(data.data);
        }, (function(_this) {
          return function(error) {
            return defer.reject(error);
          };
        })(this));
        return defer.promise;
      };
      AuthAPI.validateUser = function(refrencecode, password) {
        var defer;
        defer = $q.defer();
        App.getInstallationId().then(function(installationId) {
          var params, url;
          Push.register();
          console.log('--installtionId--');
          console.log(installationId);
          params = {
            "referenceCode": refrencecode,
            "password": password,
            "installationId": installationId
          };
          url = AUTH_URL + '/user/login';
          return App.sendRequest(url, params, AUTH_HEADERS);
        }).then(function(data) {
          return defer.resolve(data.data);
        }, (function(_this) {
          return function(error) {
            return defer.reject(error);
          };
        })(this));
        return defer.promise;
      };
      AuthAPI.setPassword = function(refrencecode, password) {
        var defer, params, url;
        params = {
          "referenceCode": refrencecode,
          "password": password
        };
        defer = $q.defer();
        url = AUTH_URL + '/user/setpassword';
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

}).call(this);
