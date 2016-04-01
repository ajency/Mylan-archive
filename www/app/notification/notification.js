angular.module('PatientApp.notification', []).controller('notifyCtrl', [
  '$scope', 'App', 'Storage', 'notifyAPI', '$rootScope', 'NotifyCount', 'CSpinner', 'CToast', function($scope, App, Storage, notifyAPI, $rootScope, NotifyCount, CSpinner, CToast) {
    $scope.view = {
      data: [],
      display: 'noError',
      page: 0,
      limit: 20,
      refcode: '',
      canLoadMore: true,
      refresh: false,
      gotAllRequests: false,
      disable: false,
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
              if (_this.refresh) {
                _this.data = data;
              } else {
                _this.data = _this.data.concat(data);
              }
              if (dataSize < _this.limit) {
                _this.canLoadMore = false;
              } else {
                _this.canLoadMore = true;
              }
            } else {
              _this.canLoadMore = false;
              _this.data = [];
            }
            if (!_this.canLoadMore) {
              _this.gotAllRequests = true;
            }
            _.each(_this.data, function(value) {
              value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('DD-MM-YYYY hh:mm A');
              return value['graceDateDisplay'] = moment(value.graceDate).format('DD-MM-YYYY hh:mm A');
            });
            return _this.onScrollComplete();
          };
        })(this), (function(_this) {
          return function(error) {
            _this.data = [];
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this))["finally"]((function(_this) {
          return function() {
            _this.disable = false;
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
          return console.log('sucess data');
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
        this.gotAllRequests = false;
        this.page = 0;
        this.canLoadMore = true;
        this.display = 'loader';
        this.refresh = true;
        this.init();
        return NotifyCount.getCount(this.refcode);
      },
      onInfiniteScroll: function() {
        this.disable = true;
        this.refresh = false;
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            _this.refcode = refcode;
            return _this.init();
          };
        })(this));
      },
      onScrollComplete: function() {
        return $scope.$broadcast('scroll.infiniteScrollComplete');
      },
      DeleteAll: function() {
        var objIds, param;
        this.canLoadMore = false;
        CSpinner.show('', 'Please wait..');
        objIds = _.pluck(this.data, 'id');
        param = {
          "notificationIds": objIds
        };
        return notifyAPI.deleteAllNotification(param).then((function(_this) {
          return function(data) {
            App.notification.count = App.notification.count - data.length;
            if (App.notification.count <= 0) {
              App.notification.badge = false;
            }
            _this.refresh = true;
            App.scrollTop();
            App.resize();
            _this.page = 0;
            return _this.init();
          };
        })(this), function(error) {
          if (error === 'offline') {
            return CToast.show('Check internet connection');
          } else if (error === 'server_error') {
            return CToast.showLongBottom('Error in clearing Notification ,Server error');
          } else {
            return CToast.showLongBottom('Error in clearing Notification ,Server error');
          }
        })["finally"](function() {
          return CSpinner.hide();
        });
      },
      onPullToRefresh: function() {
        this.disable = true;
        this.gotAllRequests = false;
        NotifyCount.getCount(this.refcode);
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
      autoFetch: function() {
        this.gotAllRequests = false;
        this.page = 0;
        this.refresh = true;
        this.canLoadMore = false;
        return this.init();
      }
    };
    $scope.$on('$ionicView.enter', function() {
      return Storage.setData('refcode', 'get').then(function(refcode) {
        return NotifyCount.getCount(refcode);
      });
    });
    return $rootScope.$on('in:app:notification', function(e, obj) {
      return $scope.view.autoFetch();
    });
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
