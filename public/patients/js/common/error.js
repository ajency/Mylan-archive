angular.module('angularApp.common').directive('ajError', [
  function() {
    return {
      restrict: 'E',
      replace: true,
      templateUrl: 'patients/views/error.html',
      scope: {
        tapToRetry: '&',
        errorType: '=',
        setTy: '='
      },
      link: function(scope, el, attr) {
        var errorMsg;
        switch (scope.errorType) {
          case 'server_connection':
            errorMsg = 'Could not connect to server';
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
