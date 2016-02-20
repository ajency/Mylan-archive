app = angular.module 'PatientApp.Global'

.directive 'textSelect', ['$timeout', ($timeout)->

  link: (scope, element, attr)->
    $('input').keyup (e) ->
      console.log 'onkey uppp'
      if $(this).val() != ''
        $('input').not(this).attr 'disabled', 'disabled'
      else
        $('input').removeAttr 'disabled'

]