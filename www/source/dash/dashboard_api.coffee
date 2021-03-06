angular.module 'PatientApp.dashboard'

.factory 'DashboardAPI', ['$q', '$http', 'App', '$stateParams', ($q, $http, App, $stateParams)->

	DashboardAPI = {}

	DashboardAPI.get = (param)->
		defer = $q.defer()

		url = PARSE_URL+'/dashboard'
				
		App.sendRequest(url, param,PARSE_HEADERS)
		.then (data)->
			defer.resolve data.data
		, (error)=>
			defer.reject error
			
		defer.promise

		# summary_data = 
		# 	array:
		# 		0:
		# 			response_id: '101'
		# 			date_time: '20-10-2015|15.30'
		# 			status:'Upcoming'
		# 			action:''
		# 			quizId:'105'
		# 		1:
		# 			response_id: '102'
		# 			date_time: '20-10-2015|15.30'
		# 			status:'Due'
		# 			action:'Start'
		# 			quizId:'106'
		# 		2:
		# 			response_id: '103'
		# 			date_time: '20-10-2015|15.30'
		# 			status:'Missed'
		# 			action:''
		# 			quizId:'107'
		# 		3:
		# 			response_id: '104'
		# 			date_time: '20-10-2015|15.30'
		# 			status:'Submitted'
		# 			action:'View'
		# 			quizId:'108'
		# 		4:
		# 			response_id: '105'
		# 			date_time: '20-10-2015|15.30'
		# 			status:'Submitted'
		# 			action:'View'
		# 			quizId:'109'	

		# summary_data



	DashboardAPI	
]