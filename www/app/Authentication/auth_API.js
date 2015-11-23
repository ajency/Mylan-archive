angular.module('PatientApp.Auth').factory('AuthAPI', [
  '$q', 'App', '$http', 'UrlList', function($q, App, $http, UrlList) {
    var AuthAPI;
    AuthAPI = {};
    AuthAPI.validateRefCode = function(refcode) {
      console.log(refcode);
      return console.log(UrlList.urlname);
    };
    AuthAPI.validateUser = function(refcode, password) {
      return console.log(refcode + password);
    };
    AuthAPI.sendPassword = function(password, UUID, devicetype, deviceOS, accessType) {
      return console.log(UrlList.urlname);
    };
    return AuthAPI;
  }
]);
