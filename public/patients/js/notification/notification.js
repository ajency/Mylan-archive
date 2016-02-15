angular.module('angularApp.notification', []).controller('notifyCtrl', [
  '$scope', 'App', '$routeParams', 'notifyAPI', '$location', function($scope, App, $routeParams, notifyAPI, $location) {
    return $scope.view = {
      data: [],
      display: 'loader',
      page: 0,
      noNotification: null,
      limit: 10,
      init: function() {
        var param;
        param = {
          "patientId": RefCode,
          "page": this.page,
          "limit": this.limit
        };
        console.log('**** notification coffeee ******');
        console.log(param);
        return notifyAPI.getNotification(param).then((function(_this) {
          return function(data) {
            var dataSize;
            console.log('notification data');
            console.log(data);
            _this.display = 'noError';
            dataSize = _.size(data);
            if (dataSize > 0) {
              if (dataSize < _this.limit) {
                _this.canLoadMore = false;
              } else {
                _this.canLoadMore = true;
              }
            } else {
              _this.canLoadMore = false;
            }
            _this.data = _this.data.concat(data);
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
        })(this))["finally"]((function(_this) {
          return function() {
            return _this.page = _this.page + 1;
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
      },
      showMore: function() {
        return this.init();
      },
      DeleteAll: function() {
        var param;
        param = {
          "patientId": RefCode
        };
        return notifyAPI.deleteAllNotification(param).then(function(data) {
          console.log('sucess notification seen data');
          return console.log(data);
        }, function(error) {
          return console.log('error data');
        });
      }
    };
  }
]);
