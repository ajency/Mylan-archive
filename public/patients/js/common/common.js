angular.module('angularApp.common', []).factory('App', [
  '$q', '$http', function($q, $http) {
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
      }
    };
  }
]);
