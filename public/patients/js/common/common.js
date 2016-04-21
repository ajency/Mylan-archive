angular.module('angularApp.common', []).factory('App', [
  '$q', '$http', '$location', '$rootScope', function($q, $http, $location, $rootScope) {
    var App;
    return App = {
      previousState: '',
      currentState: '',
      test: 4555,
      parseErrorCode: function(error) {
        var errMsg, errType;
        errType = '';
        errMsg = error.message;
        if (error.code === 100) {
          errType = 'offline';
        } else if (error.code === 141) {
          errType = 'server_error';
        } else if (errMsg.code === 101) {
          errType = 'server_error';
        } else if (errMsg.code === 124) {
          errType = 'offline';
        } else if (error.code === 209) {
          error = 'server_connection';
          $rootScope.$broadcast('on:session:expiry');
        }
        return errType;
      },
      errorCode: function(error) {
        error = '';
        if (error.code === '100') {
          error = 'server_connection';
        } else {
          error = 'server_error';
        }
        return error;
      },
      SendParseRequest: function(cloudFun, param) {
        var defer;
        defer = $q.defer();
        Parse.Cloud.run(cloudFun, param, {
          success: function(result) {
            return defer.resolve(result);
          },
          error: (function(_this) {
            return function(error) {
              console.log('inside error common function');
              console.log(error);
              return defer.reject(_this.parseErrorCode(error));
            };
          })(this)
        });
        return defer.promise;
      },
      navigate: function(path, param) {
        var location;
        location = path;
        if (!_.isEmpty(param)) {
          location += '/' + param;
        }
        console.log('***********');
        console.log(location);
        return $location.path(location);
      },
      sendRequest: function(url, params, headers, timeout) {
        var defer;
        defer = $q.defer();
        if (!_.isUndefined(timeout)) {
          headers['timeout'] = timeout;
        }
        $http.post(url, params, headers).then(function(data) {
          return defer.resolve(data);
        }, (function(_this) {
          return function(error) {
            return defer.reject(_this.errorCode(error));
          };
        })(this));
        return defer.promise;
      }
    };
  }
]);
