angular.module('angularApp.contact').factory('contactAPI', [
  '$q', '$http', 'App', function($q, $http, App, $stateParams) {
    var contactAPI;
    contactAPI = {};
    contactAPI.sendEmail = function(param) {
      var apiUrl, defer;
      defer = $q.defer();
      apiUrl = Url + '/api/v1/user/contactus';
      App.sendRequest(apiUrl, param, AUTH_HEADERS).then(function(data) {
        return defer.resolve(data.data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    return contactAPI;
  }
]);
