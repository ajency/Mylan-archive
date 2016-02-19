app = angular.module 'PatientApp.Global'

.directive 'textSelect', ['$timeout', ($timeout)->

  link: (scope, element, attr)->
    $(':text').keyup (e) ->
      if $(this).val() != ''
        $(':text').not(this).attr 'disabled', 'disabled'
      else
        $(':text').removeAttr 'disabled'

]