angular.module('PatientApp.Auth').factory('AuthAPI', [
  '$q', 'App', '$http', 'UrlList', function($q, App, $http, UrlList) {
    var AuthAPI;
    AuthAPI = {};
    AuthAPI.validateRefCode = function(refcode) {
      var defer, headers, params;
      params = {
        "referenceCode": refcode,
        "deviceType": 'mobile',
        "deviceIdentifier": '12345672',
        "deviceOS": 'ios',
        "accessType": 'app'
      };
      headers = {
        headers: {
          "Access-Control-Allow-Origin": '*',
          "Access-Control-Allow-Headers": 'Content-Type,X-Authorization',
          "X-API-KEY": 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y',
          "X-Authorization": 'e7544bd1e3743b71ea473cee30d73227135358aa',
          "Content-Type": 'application/json'
        }
      };
      defer = $q.defer();
      $http.post('http://54.213.248.21/api/v1/user/dosetup', params, headers).then(function(data) {
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
      $http.post('http://54.213.248.21/api/v1/user/login', params, headers).then(function(data) {
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
      $http.post('http://54.213.248.21/api/v1/user/setpassword', params, headers).then(function(data) {
        return defer.resolve(data.data);
      }, function(error) {
        return defer.reject(error);
      });
      return defer.promise;
    };
    return AuthAPI;
  }
]);
