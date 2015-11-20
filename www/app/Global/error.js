angular.module('PatientApp.Global').directive('ajError', [
  function() {
    return {
      restrict: 'E',
      replace: true,
      templateUrl: 'views/error-view/error.html',
      scope: {
        tapToRetry: '&',
        errorType: '='
      },
      link: function(scope, el, attr) {
        return console.log(scope.errorType);
      }
    };
  }
]);
