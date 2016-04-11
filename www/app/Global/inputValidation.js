var app;

app = angular.module('PatientApp.Global').directive('textSelect', [
  '$timeout', function($timeout) {
    return {
      link: function(scope, element, attr) {}
    };
  }
]).directive('mcqSelect', [
  '$timeout', function($timeout) {
    return {
      link: function(scope, element, attr) {
        return $timeout(function() {
          return $(element).click(function() {
            return $(element).parent().addClass('mcq_a');
          });
        });
      }
    };
  }
]);
