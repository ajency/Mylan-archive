angular.module 'PatientApp.Auth'

.factory 'AuthAPI', ['$q', 'App', '$http','UrlList', ($q, App, $http,UrlList)->
	AuthAPI = {}
	
	AuthAPI.validateRefCode = (refcode )->
		console.log refcode
		console.log UrlList.urlname
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
	
	AuthAPI.sendPassword = (password,UUID,devicetype, deviceOS,accessType)->
		# console.log refcode+UUID +  devicetype  + deviceOS 
		console.log UrlList.urlname		# defer = $q.defer()
				# $http.post '', {}
				# .then (data)->
				# 	defer.resolve data.data.result
				# , (error)->
				# 	defer.reject error

				# defer.promise					










	AuthAPI
]