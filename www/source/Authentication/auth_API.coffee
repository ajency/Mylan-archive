angular.module 'PatientApp.Auth'

.factory 'AuthAPI', ['$q', 'App', '$http', ($q, App, $http)->
	AuthAPI = {}
	
	AuthAPI.validateRefCode = (refcode ,UUID,devicetype, deviceOS,accessType)->
		console.log refcode+UUID +  devicetype  + deviceOS 
		# defer = $q.defer()
				# $http.post '', {}
				# .then (data)->
				# 	defer.resolve data.data.result
				# , (error)->
				# 	defer.reject error

				# defer.promise		

	AuthAPI.validateUser = (refcode,password)->
		console.log refcode + password
		# defer = $q.defer()
				# $http.post '', {}
				# .then (data)->
				# 	defer.resolve data.data.result
				# , (error)->
				# 	defer.reject error

				# defer.promise					
	
	AuthAPI.sendPassword = (password)->
		console.log password
		# defer = $q.defer()
				# $http.post '', {}
				# .then (data)->
				# 	defer.resolve data.data.result
				# , (error)->
				# 	defer.reject error

				# defer.promise					










	AuthAPI
]