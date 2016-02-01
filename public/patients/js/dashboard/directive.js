angular.module('angularApp.dashboard').directive('styleContainer', [
  '$timeout', function($timeout) {
    return {
      link: function(scope, element, attr) {
        return $timeout(function() {
          return $('.container_main').css('min-height', $(window).height());
        });
      }
    };
  }
]);
