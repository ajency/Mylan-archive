(function() {
  angular.module('PatientApp.Auth').factory('AuthAPI', [
    '$q', 'App', '$http', 'UrlList', function($q, App, $http, UrlList) {
      var AuthAPI;
      AuthAPI = {};
      AuthAPI.validateRefCode = function(refcode, deviceUUID, deviceOS) {
        var defer, headers, params;
        params = {
          "referenceCode": refcode,
          "deviceType": 'mobile',
          "deviceIdentifier": deviceUUID,
          "deviceOS": deviceOS,
          "accessType": 'app'
        };
        headers = {
          headers: {
            "X-API-KEY": 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y',
            "X-Authorization": 'e7544bd1e3743b71ea473cee30d73227135358aa',
            "Content-Type": 'application/json'
          }
        };
        defer = $q.defer();
        $http.post(AUTH_URL + '/user/dosetup', params, headers).then(function(data) {
          console.log('succ');
          console.log(data);
          return defer.resolve(data.data);
        }, function(error) {
          console.log('eroor');
          return defer.reject(error);
        });
        return defer.promise;
      };
      AuthAPI.validateUser = function(refrencecode, password) {
        var defer, headers, params;
        params = {
          "referenceCode": refrencecode,
          "password": password
        };
        headers = {
          headers: {
            "X-API-KEY": 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y',
            "X-Authorization": 'e7544bd1e3743b71ea473cee30d73227135358aa',
            "Content-Type": 'application/json'
          }
        };
        defer = $q.defer();
        $http.post(AUTH_URL + '/user/login', params, headers).then(function(data) {
          return defer.resolve(data.data);
        }, function(error) {
          return defer.reject(error);
        });
        return defer.promise;
      };
      AuthAPI.setPassword = function(refrencecode, password) {
        var defer, headers, params;
        params = {
          "referenceCode": refrencecode,
          "password": password
        };
        headers = {
          headers: {
            "X-API-KEY": 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y',
            "X-Authorization": 'e7544bd1e3743b71ea473cee30d73227135358aa',
            "Content-Type": 'application/json'
          }
        };
        defer = $q.defer();
        $http.post(AUTH_URL + '/user/setpassword', params, headers).then(function(data) {
          return defer.resolve(data.data);
        }, function(error) {
          return defer.reject(error);
        });
        return defer.promise;
      };
      return AuthAPI;
    }
  ]);

}).call(this);
