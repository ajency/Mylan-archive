app = angular.module 'PatientApp.Global'

app.directive 'inputvalidation', ->
  {
    require: 'ngModel'
    link: (scope, element, attrs, modelCtrl) ->
      modelCtrl.$parsers.push (inputValue) ->
        if !inputValue
          return ''
        console.log inputValue
        transformedInput = inputValue
        if transformedInput != inputValue
          modelCtrl.$setViewValue transformedInput
          modelCtrl.$render()
        transformedInput
      return

  }