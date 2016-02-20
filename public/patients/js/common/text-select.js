angular.module('angularApp.common').directive('textSelect', [
  '$timeout', function($timeout) {
    return {
      link: function(scope, element, attr) {
        return $('input').keyup(function(e) {
          console.log('onkey uppp');
          if ($(this).val() !== '') {
            return $('input').not(this).attr('disabled', 'disabled');
          } else {
            return $('input').removeAttr('disabled');
          }
        });
      }
    };
  }
]);
