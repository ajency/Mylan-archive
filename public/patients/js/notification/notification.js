angular.module('angularApp.notification', []).controller('notifyCtrl', [
  '$scope', 'App', '$routeParams', 'notifyAPI', '$location', '$rootScope', function($scope, App, $routeParams, notifyAPI, $location, $rootScope) {
    return $scope.view = {
      data: [],
      display: 'loader',
      page: 0,
      noNotification: null,
      limit: 10,
      gotAllRequests: false,
      email: hospitalEmail,
      phone: hospitalPhone,
      init: function() {
        var param;
        $rootScope.$broadcast('notification:count');
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
            if (!_this.canLoadMore) {
              _this.gotAllRequests = true;
            }
            _this.data = _this.data.concat(data);
            return _.each(_this.data, function(value) {
              value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('DD-MM-YYYY hh:mm A');
              return value['graceDateDisplay'] = moment(value.graceDate).format('DD-MM-YYYY hh:mm A');
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
        return notifyAPI.deleteNotification(param).then((function(_this) {
          return function(data) {
            var idObject, spliceIndex;
            console.log('sucess notification seen data');
            console.log(data);
            idObject = _.findWhere(_this.data, {
              id: id
            });
            if (idObject.hasSeen === false) {
              $rootScope.$broadcast('decrement:notification:count');
            }
            spliceIndex = _.findIndex($scope.view.data, function(request) {
              return request.id === id;
            });
            console.log('spliceeIndexx');
            console.log(spliceIndex);
            if (spliceIndex !== -1) {
              $scope.view.data.splice(spliceIndex, 1);
            }
            if (_this.data.length < 5) {
              return _this.init();
            }
          };
        })(this), function(error) {
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
        this.gotAllRequests = false;
        this.page = 0;
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
        var objIds, param;
        objIds = _.pluck(this.data, 'id');
        param = {
          "notificationIds": objIds
        };
        this.display = 'loader';
        return notifyAPI.deleteAllNotification(param).then((function(_this) {
          return function(data) {
            $rootScope.$broadcast('notification:count');
            _this.canLoadMore = false;
            _this.data = [];
            _this.init();
            console.log('sucess notification seen data');
            return console.log(data);
          };
        })(this), function(error) {
          return console.log('error data');
        });
      }
    };
  }
]);
