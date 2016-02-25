var app;

app = angular.module('PatientApp.Global').directive('textSelect', [
  '$timeout', function($timeout) {
    return {
      link: function(scope, element, attr) {}
    };
  }
]);
