angular.module('angularApp.common', []).factory('App', [
  '$q', '$http', '$location', function($q, $http, $location) {
    var App;
    return App = {
      SendParseRequest: function(cloudFun, param) {
        var defer;
        defer = $q.defer();
        Parse.Cloud.run(cloudFun, param, {
          success: function(result) {
            console.log('common function resulttt');
            console.log(result);
            return defer.resolve(result);
          },
          error: function(error) {
            console.log('inside error');
            console.log(error);
            return defer.reject(error);
          }
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
