angular.module('PatientApp.main', []).controller('MainCtr', [
  '$scope', 'App', 'Storage', 'notifyAPI', '$ionicLoading', 'Push', '$rootScope', function($scope, App, Storage, notifyAPI, $ionicLoading, Push, $rootScope) {
    $scope.view = {
      init: function() {
        console.log('inittt...');
        Push.register();
        return this.getNotifications();
      },
      getNotifications: function() {
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            var param;
            param = {
              "patientId": refcode
            };
            return notifyAPI.getNotification(param).then(function(data) {
              var arrSeen;
              console.log('notificato data');
              console.log(data);
              arrSeen = [];
              _.each(data, function(value) {
                if (value.hasSeen === false) {
                  return arrSeen.push(1);
                }
              });
              if (arrSeen.length > 0) {
                App.notification.count = arrSeen.length;
                return App.notification.badge = true;
              }
            });
          };
        })(this));
      },
      onBackClick: function() {
        var count;
        switch (App.currentState) {
          case 'main_login':
            if (App.previousState === 'setup_password') {
              return App.navigate("setup", {}, {
                animate: false,
                back: false
              });
            } else {
              count = -1;
              return App.goBack(count);
            }
            break;
          case 'exit-questionnaire':
            return App.navigate("dashboard", {}, {
              animate: false,
              back: false
            });
          default:
            count = -1;
            return App.goBack(count);
        }
      },
      resetPassword: function() {
        return App.navigate('reset_password');
      },
      contact: function() {
        return App.navigate('contact');
      },
      update: function() {
        return App.navigate('notification');
      },
      pause: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/main/pause.html',
          hideOnStateChange: true
        });
      },
      exitApp: function() {
        return ionic.Platform.exitApp();
      },
      closePopup: function() {
        return $ionicLoading.hide();
      }
    };
    return $rootScope.$on('in:app:notification', function(e, obj) {
      if (App.notification.count === 0) {
        return $scope.view.getNotifications();
      } else {
        return App.notification.increment();
      }
    });
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('main', {
      url: '/main',
      abstract: true,
      templateUrl: 'views/main.html',
      controller: 'MainCtr'
    });
  }
]);
