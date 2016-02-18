angular.module('PatientApp.notificationCount', []).factory('NotifyCount', [
  'notifyAPI', 'App', function(notifyAPI, App) {
    var NotifyCount;
    NotifyCount = {};
    NotifyCount.getCount = function(refcode) {
      var param;
      param = {
        "patientId": refcode
      };
      return notifyAPI.getNotificationCount(param).then(function(data) {
        if (data > 0) {
          App.notification.count = data;
          return App.notification.badge = true;
        }
      });
    };
    return NotifyCount;
  }
]);
