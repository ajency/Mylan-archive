angular.module('angularApp.common').factory('CToast', [
  '$q', '$http', function($q, $http) {
    var CToast;
    CToast = {};
    CToast.show = function(content) {
      return console.log(content);
    };
    return CToast;
  }
]);
