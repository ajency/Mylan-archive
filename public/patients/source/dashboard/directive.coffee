angular.module 'angularApp.dashboard'

.directive 'styleContainer', ['$timeout', ($timeout)->
	
	link: (scope, element, attr)->
		$timeout ->
			$('.container_main').css('min-height',$(window).height())
]