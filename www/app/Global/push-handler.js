(function() {
  angular.module('PatientApp.Global').factory('Push', [
    'App', '$rootScope', 'PushConfig', function(App, $rootScope, PushConfig) {
      var Push;
      Push = {};
      Push.register = function() {
        var PushPlugin, androidConfig, iosConfig;
        androidConfig = {
          "senderID": "DUMMY_SENDER_ID"
        };
        iosConfig = {
          "badge": true,
          "sound": true,
          "alert": true
        };
        PushPlugin = PushNotification.init(PushConfig);
        return PushPlugin.on('notification', function(data) {
          var payload;
          console.log('notification received', data);
          payload = Push.getPayload(data);
          if (!_.isEmpty(payload)) {
            return Push.handlePayload(payload);
          }
        });
      };
      Push.getPayload = function(p) {
        var payload;
        console.log(p);
        payload = {};
        if (App.isAndroid()) {
          payload = p.additionalData;
        }
        if (App.isIOS()) {
          payload = p.additionalData;
        }
        return payload;
      };
      Push.handlePayload = function(payload) {
        var inAppNotification, notificationClick;
        console.log(payload, 'Handle PayLoad');
        inAppNotification = function() {
          console.log('inApp ');
          return $rootScope.$broadcast('in:app:notification', {
            payload: payload
          });
        };
        notificationClick = function() {
          console.log('notification ');
          return $rootScope.$broadcast('push:notification:click', {
            payload: payload
          });
        };
        if (App.isAndroid()) {
          if (payload.coldstart) {
            return notificationClick();
          } else if (!payload.foreground && !payload.coldstart) {
            return notificationClick();
          } else if (payload.foreground) {
            return inAppNotification();
          } else if (!payload.foreground) {
            return inAppNotification();
          }
        } else if (App.isIOS()) {
          console.log('ios');
          console.log('----');
          console.log(payload);
          console.log('----');
          if (payload.foreground) {
            return inAppNotification();
          } else if (!payload.foreground) {
            return notificationClick();
          }
        }
      };
      return Push;
    }
  ]);

}).call(this);
