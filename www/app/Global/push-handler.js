(function() {
  angular.module('PatientApp.Global').factory('Push', [
    'App', '$cordovaPushV5', '$rootScope', function(App, $cordovaPushV5, $rootScope) {
      var Push;
      Push = {};
      Push = {
        register: function() {
          var defer;
          defer = $.Deferred();
          if (window.ParsePushPlugin) {
            ParsePushPlugin.getInstallationId((function(id) {
              console.log('device installationId: ' + id);
              return defer.resolve(id);
            }), function(e) {
              console.log('error');
              return defer.reject(e);
            });
            return defer.promise();
          }
        }
      };
      Push.getPayload = function(p) {
        var foreground, payload;
        console.log(p);
        payload = {};
        if (App.isAndroid()) {
          console.log('In android');
          payload = p;
          if (p.event === 'message') {
            payload = p;
            payload.foreground = p.foreground;
            if (_.has(p, 'coldstart')) {
              payload.coldstart = p.coldstart;
            }
          }
        }
        if (App.isIOS()) {
          console.log('In IOS');
          payload = p;
          foreground = p.foreground === "1" ? true : false;
          payload.foreground = foreground;
        }
        return payload;
      };
      Push.handlePayload = function(payload) {
        var inAppNotification, notificationClick;
        inAppNotification = function() {
          console.log('inApp ', payload);
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
