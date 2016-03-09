angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI','HospitalData', 'NotifyCount'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData, NotifyCount)->

		$scope.view =
			hospitalName: HospitalData.name
			projectName : HospitalData.project
			SubmissionData : []
			data : []
			display : 'loader'
			infoMsg : null
			limitTo: 5
			showMoreButton : true

			onPullToRefresh :->
				@showMoreButton = true
				@data =[]
				@getSubmission()
				@limitTo = 5


			init :() ->
				Storage.getNextQuestion 'set' , 1

			startQuiz :(val) ->
				App.navigate 'start-questionnaire', responseId:val

			getSubmission : ->
				@display = 'loader'
				@showMoreButton = false
				
				Storage.setData 'refcode','get'
				.then (refcode)=>
					param = 
						"patientId": refcode
					DashboardAPI.get param
					.then (data)=>

						console.log 'dashoard data'
						console.log data
						@data = data
						# arr = _.reject(@data, function(d){ return d.status === 'base_line'; })
						arr = _.reject(@data, (d) -> d.status == 'base_line')
						if arr.length < 6
							@showMoreButton = false
						else
							@showMoreButton = true
						
						arr = []
						if !_.isEmpty(_.where(@data, {status: "due"})) 
							 arr.push _.where(@data, {status: "due"})
						
						if !_.isEmpty(_.where(@data, {status: "started"})) 
							 arr.push _.where(@data, {status: "started"})

						if !_.isEmpty(_.where(@data, {status: "late"})) 
							 arr.push _.where(@data, {status: "late"})

						if arr.length == 0 
							@infoMsg = true
						else
							@infoMsg = false


						_.each @data, (value)->
							value.occurrenceDate = moment(value.occurrenceDate).format('MMMM Do YYYY')
						@display = 'noError'
					,(error)=>
						@display = 'error'
						@errorType = error
					.finally =>
						$scope.$broadcast 'scroll.refreshComplete'
						App.resize()

			displaydata : ->
				# @data = []
				@getSubmission()	
				

			summary : (id)->
				App.navigate 'summary', summary:id

			resumeQuiz : (id)->
				App.navigate 'questionnaire', respStatus:id

			onTapToRetry : ->
				@display = 'loader'
				@getSubmission()

			showMore : ->
				@limitTo = @limitTo + 5
				App.resize()
				if @data.length < @limitTo 
					@showMoreButton = false

		$scope.$on '$ionicView.enter', (event, viewData)->
			console.log 'view enter'
			$scope.view.displaydata()
			Storage.setData 'refcode','get'
				.then (refcode)->
					NotifyCount.getCount(refcode)

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.display = 'loader'
			$scope.view.data = []
			$scope.view.limitTo = 5
			$scope.view.showMoreButton = false

]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'dashboard',
			url: '/dashboard'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/dashboard/dashboard.html'
					controller: 'DashboardCtrl'
					cache: false
					resolve:
						HospitalData :($q, Storage)->
							defer = $q.defer()
							Storage.hospital_data 'get'
							.then (data)->
								defer.resolve data
							defer.promise

]

			
