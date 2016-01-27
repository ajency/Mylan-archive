angular.module('PatientApp.notification', []).controller('notifyCtrl', [
  '$scope', 'App', 'Storage', 'notifyAPI', function($scope, App, Storage, notifyAPI) {
    return $scope.view = {
      data: [],
      display: 'loader',
      init: function() {
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            var param;
            ({
              display: 'loader'
            });
            param = {
              "patientId": refcode
            };
            return notifyAPI.getNotification(param).then(function(data) {
              console.log('notification data');
              console.log(data);
              _this.display = 'noError';
              _this.data = [];
              _this.data = data;
              return _.each(_this.data, function(value) {
                value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY');
                return value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY');
              });
            });
          };
        })(this), (function(_this) {
          return function(error) {
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this));
      },
      seenNotify: function(id) {
        var param;
        console.log('********');
        console.log(id);
        App.navigate('dashboard', {}, {
          animate: false,
          back: false
        });
        param = {
          "notificationId": id
        };
        return notifyAPI.setNotificationSeen(param).then(function(data) {
          console.log('sucess data');
          return console.log(data);
        }, function(error) {
          return console.log('error data');
        });
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.init();
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('notification', {
      url: '/notification',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/notification/notification.html',
          controller: 'notifyCtrl'
        }
      }
    });
  }
]);
