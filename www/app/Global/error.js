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
        var errorMsg;
        switch (scope.errorType) {
          case 'offline':
            errorMsg = 'No internet availability';
            break;
          case 'server_error':
            errorMsg = 'Server error';
            break;
          default:
            errorMsg = 'Unknown error';
        }
        scope.errorMsg = errorMsg;
        return scope.onTryAgain = function() {
          return scope.tapToRetry();
        };
      }
    };
  }
]);
