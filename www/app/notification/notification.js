angular.module('PatientApp.notification', []).controller('notifyCtrl', [
  '$scope', 'App', 'Storage', 'notifyAPI', '$rootScope', function($scope, App, Storage, notifyAPI, $rootScope) {
    return $scope.view = {
      data: [],
      display: 'loader',
      page: 0,
      limit: 10,
      refcode: '',
      canLoadMore: true,
      refresh: false,
      init: function() {
        var param;
        param = {
          "patientId": this.refcode,
          "page": this.page,
          "limit": this.limit
        };
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
            if (_this.refresh) {
              _this.data = data;
            } else {
              _this.data = _this.data.concat(data);
            }
            _.each(_this.data, function(value) {
              value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY');
              return value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY');
            });
            return _this.onScrollComplete();
          };
        })(this), (function(_this) {
          return function(error) {
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this))["finally"]((function(_this) {
          return function() {
            _this.page = _this.page + 1;
            $scope.$broadcast('scroll.refreshComplete');
            return $scope.$broadcast('scroll.infiniteScrollComplete');
          };
        })(this));
      },
      seenNotify: function(id) {
        var idObject, param;
        App.navigate('dashboard', {}, {
          animate: false,
          back: false
        });
        param = {
          "notificationId": id
        };
        notifyAPI.setNotificationSeen(param).then(function(data) {
          console.log('sucess data');
          return console.log(data);
        }, function(error) {
          return console.log('error data');
        });
        idObject = _.findWhere(this.data, {
          id: id
        });
        if (idObject.hasSeen === false) {
          return App.notification.decrement();
        }
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.init();
      },
      onInfiniteScroll: function() {
        this.refresh = false;
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            _this.refcode = refcode;
            console.log('iii');
            return _this.init();
          };
        })(this));
      },
      onScrollComplete: function() {
        return $scope.$broadcast('scroll.infiniteScrollComplete');
      },
      DeleteAll: function() {
        var param;
        param = {
          "patientId": this.refcode
        };
        return notifyAPI.deleteAllNotification(param).then((function(_this) {
          return function(data) {
            console.log('sucess notification seen data');
            console.log(data);
            _this.data = [];
            App.notification.count = 0;
            return App.notification.badge = false;
          };
        })(this), function(error) {
          console.log('error data');
          if (error === 'offline') {
            return CToast.show('Check net connection');
          } else if (error === 'server_error') {
            return CToast.showLongBottom('Error in clearing Notification ,Server error');
          } else {
            return CToast.showLongBottom('Error in clearing Notification ,Server error');
          }
        });
      },
      onPullToRefresh: function() {
        this.page = 0;
        this.refresh = true;
        this.canLoadMore = false;
        return this.init();
      },
      deleteNotify: function(id) {
        var idObject, param;
        param = {
          "notificationId": id
        };
        notifyAPI.deleteNotification(param).then((function(_this) {
          return function(data) {
            var spliceIndex;
            spliceIndex = _.findIndex($scope.view.data, function(request) {
              return request.id === id;
            });
            if (spliceIndex !== -1) {
              return $scope.view.data.splice(spliceIndex, 1);
            }
          };
        })(this));
        idObject = _.findWhere(this.data, {
          id: id
        });
        if (idObject.hasSeen === false) {
          return App.notification.decrement();
        }
      },
      getNotificationCount: function() {
        return $rootScope.$broadcast('notification:count:update');
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
