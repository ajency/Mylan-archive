(function() {
  angular.module('PatientApp.notification').factory('notifyAPI', [
    '$q', '$http', 'App', function($q, $http, App, $stateParams) {
      var notifyAPI;
      notifyAPI = {};
      notifyAPI.getNotification = function(param) {
        var defer;
        defer = $q.defer();
        App.SendParseRequest('getPatientNotifications', param).then(function(data) {
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
        App.SendParseRequest('hasSeenNotification', param).then(function(data) {
          return defer.resolve(data);
        }, (function(_this) {
          return function(error) {
            return defer.reject(error);
          };
        })(this));
        return defer.promise;
      };
      notifyAPI.getNotificationCount = function(param) {
        var defer;
        defer = $q.defer();
        App.SendParseRequest('getPatientNotificationCount', param).then(function(data) {
          return defer.resolve(data);
        }, (function(_this) {
          return function(error) {
            return defer.reject(error);
          };
        })(this));
        return defer.promise;
      };
      notifyAPI.deleteAllNotification = function(param) {
        var defer;
        defer = $q.defer();
        App.SendParseRequest('clearNotificationByIds', param).then(function(data) {
          return defer.resolve(data);
        }, (function(_this) {
          return function(error) {
            return defer.reject(error);
          };
        })(this));
        return defer.promise;
      };
      notifyAPI.deleteNotification = function(param) {
        var defer;
        defer = $q.defer();
        App.SendParseRequest('clearNotification', param).then(function(data) {
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

}).call(this);
