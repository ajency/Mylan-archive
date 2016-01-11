angular.module('angularApp.common', []).factory('App', [
  '$q', '$http', '$location', function($q, $http, $location) {
    var App;
    return App = {
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
              return defer.reject(_this.errorCode(error));
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
      }
    };
  }
]);
