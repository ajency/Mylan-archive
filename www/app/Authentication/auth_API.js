angular.module('PatientApp.Auth').factory('AuthAPI', [
  '$q', 'App', '$http', 'UrlList', function($q, App, $http, UrlList) {
    var AuthAPI;
    AuthAPI = {};
    AuthAPI.validateRefCode = function(refcode, UUID, devicetype, deviceOS, accessType) {
      console.log(refcode + UUID + devicetype + deviceOS);
      return console.log(UrlList.urlname);
    };
    AuthAPI.validateUser = function(refcode, password) {
      return console.log(refcode + password);
    };
    AuthAPI.sendPassword = function(password) {
      return console.log(password);
    };
    return AuthAPI;
  }
]);
