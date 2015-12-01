(function() {
  var app;

  app = angular.module('PatientApp.Global');

  app.directive('inputvalidation', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, modelCtrl) {
        modelCtrl.$parsers.push(function(inputValue) {
          var transformedInput;
          if (!inputValue) {
            return '';
          }
          console.log(inputValue);
          transformedInput = inputValue;
          if (transformedInput !== inputValue) {
            modelCtrl.$setViewValue(transformedInput);
            modelCtrl.$render();
          }
          return transformedInput;
        });
      }
    };
  });

}).call(this);
