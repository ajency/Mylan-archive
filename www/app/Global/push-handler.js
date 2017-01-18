(function() {
  angular.module('PatientApp.Global').factory('Push', [
    'App', '$rootScope', function(App, $rootScope) {
      var Push;
      Push = {};
      Push.register = function() {
        if (App.isWebView()) {
          return ParsePushPlugin.register(function(success) {
            return console.log('Push Registration Success');
          }, function(error) {
            return console.log('Push Registration Error');
          });
        }
      };
      Push.getPayload = function(p) {
        var foreground, payload;
        console.log(p);
        payload = {};
        if (App.isAndroid()) {
          if (p.event === 'message') {
            payload = p.payload.data;
            payload.foreground = p.foreground;
            if (_.has(p, 'coldstart')) {
              payload.coldstart = p.coldstart;
            }
          }
        }
        if (App.isIOS()) {
          payload = p;
          foreground = p.foreground === "1" ? true : false;
          payload.foreground = foreground;
        }
        return payload;
      };
      Push.handlePayload = function(payload) {
        var inAppNotification, notificationClick;
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
          } else if (!payload.foreground && !_.isUndefined(payload.coldstart) && !payload.coldstart) {
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
