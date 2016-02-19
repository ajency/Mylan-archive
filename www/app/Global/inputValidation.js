var app;

app = angular.module('PatientApp.Global').directive('textSelect', [
  '$timeout', function($timeout) {
    return {
      link: function(scope, element, attr) {
        return $(':text').keyup(function(e) {
          if ($(this).val() !== '') {
            return $(':text').not(this).attr('disabled', 'disabled');
          } else {
            return $(':text').removeAttr('disabled');
          }
        });
      }
    };
  }
]);
