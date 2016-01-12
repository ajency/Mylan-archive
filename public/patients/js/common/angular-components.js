angular.module('angularApp.common').factory('CToast', [
  '$q', '$http', function($q, $http) {
    var CToast;
    CToast = {};
    CToast.show = function(content) {
      return $("#notify-css").notify(content, {
        position: "right"
      });
    };
    return CToast;
  }
]);
