angular.module('PatientApp.Quest').directive('mcqSelect', [
  '$timeout', function($timeout) {
    return {
      link: function(scope, element, attr) {
        return $timeout(function() {
          return $('.mcq').click(function() {
            if ($(this).hasClass('mcq_active')) {
              return $(this).removeClass('mcq_active');
            } else {
              return $(this).addClass('mcq_active');
            }
          });
        });
      }
    };
  }
]);
