angular.module('PatientApp.Global').directive('ajError', [
  function() {
    return {
      restrict: 'E',
      replace: true,
      templateUrl: 'views/error-view/error.html',
      scope: {
        tapToRetry: '&',
        errorType: '=',
        setTy: '='
      },
      link: function(scope, el, attr) {
        scope.errorMsg = scope.errorType;
        return scope.onTryAgain = function() {
          return scope.tapToRetry();
        };
      }
    };
  }
]);
