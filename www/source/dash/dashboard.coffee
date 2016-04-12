angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI','HospitalData', 'NotifyCount', '$rootScope'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData, NotifyCount, $rootScope)->

		$scope.view =
			hospitalName: HospitalData.name
			projectName : HospitalData.project
			SubmissionData : []
			data : []
			display : 'loader'
			infoMsg : null
			limitTo: 5
			showMoreButton : true
			scroll : false
			errorStartQuestion : false
			errorMsg :''

			onPullToRefresh :->
				@showMoreButton = false
				@getSubmission()
				@limitTo = 5
				@scroll = false


			init :() ->
				Storage.getNextQuestion 'set' , 1

			startQuiz :(val) ->
				App.navigate 'start-questionnaire', responseId:val

			getSubmission : ->
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
				if Storage.getQuestStatus('get','questionnarireError') == 'questionnarireError'
					@errorStartQuestion = true
					@errorMsg = 'An error occurred while starting questionnaire. Please try again'
				if Storage.getQuestStatus('get','questionnarireError') == 'offline'
					@errorStartQuestion = true
					@errorMsg = 'Unable to start questionnaire. Please check your internet connection.'
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

				if @limitTo >= 25
					@scroll = true

			scrollTop : ->
				App.scrollTop()
				@scroll = false

			getScrollPosition : ->

				console.log 'getscroll position'
				scrollPosition = App.getScrollPosition()
				console.log scrollPosition
				if scrollPosition < 200
					$scope.$apply ->
						$scope.view.scroll = false
				else if scrollPosition > 1000
					if @limitTo >= 25
						$scope.$apply ->
							$scope.view.scroll = true

			scrollButtom : ->
				App.scrollBottom()

					
			

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
			$scope.view.scroll =  false
			$scope.view.errorStartQuestion = false

		$rootScope.$on 'in:app:notification', (e, obj)->
			App.scrollTop()
			$scope.view.onPullToRefresh()


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

			
