angular.module('angularApp.notification', []).controller('notifyCtrl', [
  '$scope', 'App', '$routeParams', 'notifyAPI', '$location', function($scope, App, $routeParams, notifyAPI, $location) {
    return $scope.view = {
      data: [],
      display: 'loader',
      init: function() {
        var param;
        console.log('inside notification controller');
        param = {
          "patientId": RefCode,
          "page": 1,
          "limit": 10
        };
        console.log('**** notification coffeee ******');
        console.log(param);
        return notifyAPI.getNotification(param).then((function(_this) {
          return function(data) {
            console.log('notification data');
            console.log(data);
            _this.display = 'noError';
            _this.data = [];
            _this.data = data;
            return _.each(_this.data, function(value) {
              value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY');
              return value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY');
            });
          };
        })(this), (function(_this) {
          return function(error) {
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this));
      },
      deleteNotify: function(id) {
        var param;
        console.log('***1deleteNotifcation****');
        console.log(id);
        param = {
          "notificationId": id
        };
        return notifyAPI.deleteNotification(param).then(function(data) {
          var spliceIndex;
          console.log('sucess notification seen data');
          console.log(data);
          spliceIndex = _.findIndex($scope.view.data, function(request) {
            return request.id === id;
          });
          console.log('spliceeIndexx');
          console.log(spliceIndex);
          if (spliceIndex !== -1) {
            return $scope.view.data.splice(spliceIndex, 1);
          }
        }, function(error) {
          return console.log('error data');
        });
      },
      seenNotify: function(id) {
        var param;
        console.log('***seenNotifcation****');
        console.log(id);
        param = {
          "notificationId": id
        };
        notifyAPI.setNotificationSeen(param).then(function(data) {
          console.log('sucess notification seen data');
          return console.log(data);
        }, function(error) {
          return console.log('error data');
        });
        return $location.path('dashboard');
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.init();
      },
      deleteNotifcation: function(id) {
        console.log('***deleteNotifcation****');
        return console.log(id);
      }
    };
  }
]);
