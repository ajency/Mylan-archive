angular.module('PatientApp.notification').factory('notifyAPI', [
  '$q', '$http', 'App', function($q, $http, App, $stateParams) {
    var notifyAPI;
    notifyAPI = {};
    notifyAPI.getNotification = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getAllNotifications', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    notifyAPI.setNotificationSeen = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getAllNotifications', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    return notifyAPI;
  }
]);
