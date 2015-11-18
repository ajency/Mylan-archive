angular.module('PatientApp.Auth').factory('AuthAPI', [
  '$q', 'App', '$http', function($q, App, $http) {
    var AuthAPI;
    AuthAPI = {};
    AuthAPI.validateRefCode = function(refcode, UUID, devicetype, deviceOS, accessType) {
      return console.log(refcode + UUID + devicetype + deviceOS);
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
