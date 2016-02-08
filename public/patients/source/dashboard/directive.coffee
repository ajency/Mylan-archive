angular.module 'angularApp.dashboard'

.directive 'styleContainer', ['$timeout', ($timeout)->
	
	link: (scope, element, attr)->
		$timeout ->
			$('.container_main').css('min-height',$(window).height())
]

# .directive 'validPrice', ->
#   {
#     require: 'ngModel'
#     link: (scope, elm, attrs, ctrl) ->
#       regex = /^\d{2,4}(\.\d{1,2})?$/
#       ctrl.$parsers.unshift (viewValue) ->
#         floatValue = parseFloat(viewValue)
#         if floatValue >= 50 and floatValue <= 5000 and regex.test(viewValue)
#           console.log 'if'
#           # ctrl.$setValidity 'validPrice', true
#           #return viewValue;
#         else
#         	console.log 'else'
#           # ctrl.$setValidity 'validPrice', false
#         viewValue
#       return

#   }
