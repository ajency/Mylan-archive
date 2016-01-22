angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI','HospitalData'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData)->

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

			startQuiz :() ->
				App.navigate 'start-questionnaire'

			getSubmission : ->
				@display = 'loader'
				Storage.setData 'refcode','get'
				.then (refcode)=>
					param = 
						"patientId": refcode
					DashboardAPI.get param
					.then (data)=>
						console.log 'dashoard data'
						console.log data
						@data = data
						arr = []
						if !_.isEmpty(_.where(@data, {status: "due"})) 
							 arr.push _.where(@data, {status: "due"})
						
						if !_.isEmpty(_.where(@data, {status: "started"})) 
							 arr.push _.where(@data, {status: "started"})
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
				@data = []
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

			
