cronjobRunTime = 60 #secs

Parse.Cloud.define "testNotifications", (request, response) ->
	getNotifications()
	.then () ->
		sendNotifications() 
		.then () ->
			currentDate = moment().format()
			convertedDate = convertToZone(currentDate,'Asia/Calcutta').format()
			response.success ("moment = #{currentDate} date = #{convertedDate}")
		, (error) ->
			response.error error
	, (error) ->
		response.error error


getNotifications = () ->
	promise = new Parse.Promise()

	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.exists('patient')
	#scheduleQuery.equalTo('patient', '99887766')
	scheduleQuery.include('questionnaire')
	scheduleQuery.find()
	.then (scheduleObjs) ->
		getAllNotifications = ->
			promise1 = Parse.Promise.as()
			_.each scheduleObjs, (scheduleObj) ->
				promise1 = promise1
				.then () ->
					scheduleObj.fetch()
					.then () ->
						getNotificationType(scheduleObj)
						.then (notificationType) ->
							if notificationType != ""
								notificationObj = new Parse.Object('Notification')
								notificationObj.set 'hasSeen', false
								notificationObj.set 'patient', scheduleObj.get('patient')
								notificationObj.set 'type',	notificationType
								notificationObj.set 'processed', false
								notificationObj.set 'schedule', scheduleObj
								notificationObj.set 'occurrenceDate', scheduleObj.get('nextOccurrence')
								notificationObj.save()
							else
								dummy = new Parse.Promise()
								dummy.resolve()
								dummy
						, (error) ->
							promise.reject error
					, (error) ->
						promise1.reject error
				, (error) ->
					promise1.reject error
			promise1
		getAllNotifications()
		.then () ->
			promise.resolve()
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise


getNotificationType = (scheduleObj) ->
	promise = new Parse.Promise()

	questionnaireQuery = new Parse.Query('Questionnaire')
	questionnaireQuery.get(scheduleObj.get('questionnaire').id)
	questionnaireQuery.first()
	.then (questionnaireObj) ->
		nextOccurrence = scheduleObj.get('nextOccurrence')
		currentDate = new Date()
		graceDate =new Date(scheduleObj.get('nextOccurrence').getTime() + (questionnaireObj.get('gracePeriod') * 1000))
		reminderTime = questionnaireObj.get('reminderTime')
		beforeReminder = new Date(nextOccurrence.getTime() - reminderTime * 1000)
		afterReminder =  new Date(nextOccurrence.getTime() + reminderTime * 1000)
		bufferTime = cronjobRunTime/2

		#if (currentDate.getTime() == (nextOccurrence.getTime() - (reminderTime * 1000)))
		if (currentDate.getTime() >= (nextOccurrence.getTime() - (reminderTime * 1000) - (bufferTime * 1000))) and (currentDate.getTime() <= (nextOccurrence.getTime() - (reminderTime * 1000) + (bufferTime * 1000)))
			promise.resolve("beforOccurrence")
		#else if (currentDate.getTime() == (graceDate.getTime() - (reminderTime * 1000)))
		else if (currentDate.getTime() >= (graceDate.getTime() - (reminderTime * 1000) - (bufferTime * 1000))) and (currentDate.getTime() <= (graceDate.getTime() - (reminderTime * 1000) + (bufferTime * 1000)))
			promise.resolve("beforeGracePeriod")
		#else if currentDate.getTime() >= graceDate.getTime()
		#else if currentDate.getTime() >= graceDate.getTime()
		#	promise.resolve("missedOccurrence")
		else
			promise.resolve("")
	, (error) ->
		promise.reject error
	promise




getNotificationMessage = (scheduleObj, notificationType, occurrenceDate,installationId) ->
	promise = new Parse.Promise()

	questionnaireQuery = new Parse.Query('Questionnaire')
	questionnaireQuery.get(scheduleObj.get('questionnaire').id)
	questionnaireQuery.first()
	.then (questionnaireObj) ->
		# nextOccurrence = scheduleObj.get('nextOccurrence')
		
		# console.log "-=-=-=-=-=-=-"
		# console.log "nextOccurrence #{nextOccurrence}"
		# console.log "currentDate #{currentDate}"
		# console.log "graceDate #{graceDate}"
		# console.log "reminderTime #{reminderTime}"
		# console.log "beforeReminder #{beforeReminder}"
		# console.log "afterReminder #{afterReminder}"
		# console.log "-=-=-=-=-=-=-"
		timeZoneConverter(installationId,occurrenceDate)
		.then (convertedTimezoneObject) ->
			newNextOccurrence = convertedTimezoneObject['occurrenceDate']
			timeZone = convertedTimezoneObject['timeZone'] 
			console.log "**New newNextOccurrence**"
			console.log newNextOccurrence
			gracePeriod = questionnaireObj.get("gracePeriod")
			# graceDate =new Date(newNextOccurrence.getTime() + (questionnaireObj.get('gracePeriod') * 1000))
			# "DD-MM-YYYY HH:mm HH:mm"
			graceDate = moment(occurrenceDate).add(gracePeriod, 's').format()
			if timeZone!=''
				convertedGraceDate = momenttimezone.tz(graceDate, timeZone).format('DD-MM-YYYY HH:mm')
			else
				convertedGraceDate = graceDate

			console.log "convertedGraceDate"
			console.log convertedGraceDate

			if notificationType == "beforOccurrence"
				message="Questionnaire is due on #{newNextOccurrence}"
			else if notificationType == "beforeGracePeriod"
				message="Questionnairre was due on #{newNextOccurrence}. Please submit it by #{convertedGraceDate}"
			else if notificationType == "missedOccurrence"
				message="You have missed the questionnaire due on #{newNextOccurrence}"
			else
				message=""

			console.log "**Notification Msg occurrenceDate**"
			console.log occurrenceDate
			promise.resolve(message)
		, (error) ->
			promise.reject error

	, (error) ->
		promise.reject error
	promise




sendNotifications = () ->
	Arr = []
	promise = new Parse.Promise()
	notificationQuery = new Parse.Query('Notification')
	notificationQuery.equalTo('processed', false)
	notificationQuery.find()
	.then (notifications) ->
		getNotifications = ->
			#console.log "---------------------"
			#console.log "notifications.length #{notifications.length}"
			#console.log "---------------------"
			promise1 = Parse.Promise.as()
			_.each notifications, (notification) ->
				promise1 = promise1
				.then () ->
					notification.set 'processed', true
					notification.save()
					.then (notification) ->
						#console.log "notification_1 #{notification.id}"
						#console.log "---------------------"
						scheduleQuery = new Parse.Query('Schedule')
						scheduleQuery.equalTo('patient', notification.get('patient'))
						scheduleQuery.first()
						.then (scheduleObj) ->
							#console.log "scheduleObj #{scheduleObj.id}"
							#console.log "---------------------"
							tokenStorageQuery = new Parse.Query('TokenStorage')
							tokenStorageQuery.equalTo('referenceCode', notification.get('patient'))
							tokenStorageQuery.find(useMasterKey: true)
							.then (tokenStorageObjs) ->
								sendToInstallations = ->
									promise2 = Parse.Promise.as()
									_.each tokenStorageObjs, (tokenStorageObj) ->
										promise2 = promise2
										.then () ->
											#console.log "tokenStorageObjs #{tokenStorageObjs.length}"
											#console.log "---------------------"
											getNotificationMessage(scheduleObj, notification.get('type'), notification.get('occurrenceDate'),tokenStorageObj.get('installationId'))
											.then (message) ->	
												console.log "message #{message}"
												console.log "---------------------"							
												installationQuery = new Parse.Query(Parse.Installation)
												installationQuery.equalTo('installationId', tokenStorageObj.get('installationId'))
												#installationQuery.equalTo('installationId', '4975e846-af7a-4113-b0c4-c73117908ef7')
												installationQuery.limit(1)
												installationQuery.find()
												Parse.Push.send({
													where: installationQuery
													data: {
														id: notification.id
														header: "Mylan"
														message: message}
												})
											, (error) ->
												promise1.reject error	
										,(error) ->
											console.log "send1"
											promise2.reject error
									promise2
								sendToInstallations()
							, (error) ->
								console.log "send2"
								promise1.reject error
						, (error) ->
							console.log "send3"
							promise1.reject error
					, (error) ->
						console.log "send4"
						promise1.reject error
				, (error) ->
					console.log "send5"
					promise1.reject error
			promise1
		getNotifications()
		.then () ->
				promise.resolve(Arr)
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise





Parse.Cloud.define "createMissedResponse", (request, response) ->
	createMissedResponse()
	.then (responses) ->
		response.success responses
	, (error) ->
		response.error error




Parse.Cloud.define 'deleteResponse', (request, response) ->
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('sequenceNumber', 101)
	#responseQuery.doesNotExist('occurrenceDate')
	#responseQuery.equalTo('answeredQuestions', [])
	responseQuery.equalTo('status', 'missed')
	responseQuery.find()
	.then (responses) ->
		getResponse = ->
			promise = Parse.Promise.as()
			_.each responses, (responseObj) ->
				promise = promise
				.then () ->
					console.log "111"
					responseObj.destroy()
			promise
		getResponse()
		.then () ->
			response.success "done"
	, (error) ->
		response.error error

checkMissedResponses = () ->
	promise = new Parse.Promise()
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('status', 'started')
	responseQuery.include('questionnaire')
	responseQuery.include('schedule')
	responseQuery.find()
	.then (responseObjs) ->
		updateResponses = () ->
			promise1 = Parse.Promise.as()
			_.each responseObjs, (responseObj) ->
				promise1 = promise1
				.then () ->
					timeObj = getValidTimeFrame(responseObj.get('questionnaire'), responseObj.get('occurrenceDate'))
					currentDate = new Date()
					if currentDate.getTime() > timeObj['upperLimit']
						notificationObj = new Parse.Object('Notification')
						notificationObj.set 'hasSeen', false
						notificationObj.set 'patient', responseObj.get('patient')
						notificationObj.set 'type',	'missedOccurrence'
						notificationObj.set 'processed', false
						notificationObj.set 'schedule', responseObj.get('schedule')
						notificationObj.set 'occurrenceDate', responseObj.get('occurrenceDate')
						notificationObj.save()
						.then (notificationObj) ->
							responseObj.set 'status', 'missed'
							responseObj.save()
					else
						responseObj.save()
				, (error) ->
					promise.reject error
			promise1
			#promise.resolve responseObjs
		updateResponses()
		.then () ->
			promise.resolve("done")
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise




createMissedResponse = () ->
	promise = new Parse.Promise()
	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.exists('patient')
	scheduleQuery.include('questionnaire')
	scheduleQuery.find()
	.then (scheduleObjs) ->
		updateMissedResponse = ->
			promise1 = Parse.Promise.as()
			_.each scheduleObjs, (scheduleObj) ->
				promise1 = promise1
				.then () ->
					timeObj = getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence'))
					currentDate = new Date()
					if currentDate.getTime() > timeObj['upperLimit'].getTime()
						createResponse(scheduleObj.get('questionnaire').id, scheduleObj.get('patient'), scheduleObj)
						.then (responseObj) ->
							responseObj.set 'occurrenceDate', scheduleObj.get('nextOccurrence')
							responseObj.set 'status', 'missed'
							responseObj.save()
							.then (responseObj) ->
								notificationObj = new Parse.Object('Notification')
								notificationObj.set 'hasSeen', false
								notificationObj.set 'patient', scheduleObj.get('patient')
								notificationObj.set 'type',	'missedOccurrence'
								notificationObj.set 'processed', false
								notificationObj.set 'schedule', scheduleObj
								notificationObj.set 'occurrenceDate', scheduleObj.get('nextOccurrence')
								notificationObj.save()
								.then (notificationObj) ->
									scheduleQuery = new Parse.Query('Schedule')
									scheduleQuery.doesNotExist('patient')
									scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'))
									scheduleQuery.first()
									.then (scheduleQuestionnaireObj) ->
										newNextOccurrence = new Date (scheduleObj.get('nextOccurrence').getTime())
										newNextOccurrence.setTime(newNextOccurrence.getTime() + Number(scheduleQuestionnaireObj.get('frequency')) * 1000)
										scheduleObj.set 'nextOccurrence', newNextOccurrence
										scheduleObj.save()
									, (error) ->
										console.log  "missed1"
										promise1.reject error
								, (error) ->
									promise1.reject error
							, (error) ->
								console.log  "missed2"
								promise1.reject error
						, (error) ->
							console.log  "missed3"
							promise1.reject error
					else 
						scheduleObj.save()
				, (error) ->
					console.log  "missed4"
					promise1.reject error
			promise1
		updateMissedResponse()
		.then () ->
			promise.resolve("done")
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise




Parse.Cloud.job 'commonJob', (request, response) ->
	console.log "=================="
	checkMissedResponses()
	.then (result) ->
		console.log "result = #{result}"
		createMissedResponse()
		.then (responses) ->
			console.log "responses = #{responses.length}"
			getNotifications()
			.then (notifications) ->
				console.log "notifications = #{notifications}"
				sendNotifications() 
				.then (notifications) ->
					console.log "notifications_sent = #{notifications}"
					console.log  (new Date())
					response.success "job_run"
				, (error) ->
					response.error "not_run"
			, (error) ->
				promise.reject error				
		, (error) ->
			response.error error
	, (error) ->
		response.error error





getQuestionnaireFrequency =  ( questionnaireObj ) ->
    promise = new Parse.Promise()

    questionnaireScheduleQuery = new Parse.Query('Schedule')
    questionnaireScheduleQuery.equalTo("questionnaire", questionnaireObj)
    questionnaireScheduleQuery.first()
    .then (questionnaireScheduleObj) ->
        promise.resolve questionnaireScheduleObj.get("frequency")
    , (error) ->
        promise.resolve error

    promise


Parse.Cloud.define "getAllNotifications", (request, response) ->
	patientId = request.params.patientId
	getAllNotifications(patientId)
	.then (notifications) ->
		response.success  notifications
	, (error) ->
		response.error error



getAllNotifications = (patientId) ->
	promise = new Parse.Promise()
	notificationQuery = new Parse.Query('Notification')
	notificationQuery.equalTo('patient', patientId)
	notificationQuery.include('schedule')
	notificationQuery.find()
	.then (notifications) ->
		notificationMessages = []

		getAll = () ->
			promise1 = Parse.Promise.as()
			_.each notifications, (notification) ->
				promise1 = promise1
				.then () ->
					getNotificationSendObject(notification.get('schedule'), notification)
					.then (notificationSendObject) ->
						notificationMessages.push(notificationSendObject)
						dummy = new Parse.Promise()
						dummy.resolve()
						dummy
					, (error) ->
						promise1.reject error
				, (error) ->
					promise1.reject error
			promise1
		getAll()
		.then () ->
			promise.resolve(notificationMessages)
		, (error) ->
			promise.reject(error)
	, (error) ->
		promise.reject error
	promise


getNotificationSendObject = (scheduleObj, notification) ->
	promise = new Parse.Promise()

	questionnaireQuery = new Parse.Query('Questionnaire')
	questionnaireQuery.get(scheduleObj.get('questionnaire').id)
	questionnaireQuery.first()
	.then (questionnaireObj) ->
		occurrenceDate = notification.get('occurrenceDate')
		graceDate =new Date(scheduleObj.get('nextOccurrence').getTime() + (questionnaireObj.get('gracePeriod') * 1000))
		notificationType = notification.get('type')
		#console.log "-=-=-=-=-=-=-"
		#console.log "nextOccurrence #{nextOccurrence}"
		#console.log "currentDate #{currentDate}"
		#console.log "graceDate #{graceDate}"
		#console.log "reminderTime #{reminderTime}"
		#console.log "beforeReminder #{beforeReminder}"
		#console.log "afterReminder #{afterReminder}"
		#console.log "-=-=-=-=-=-=-"	
		notificationSendObject = {}
		notificationSendObject['occurrenceDate'] = occurrenceDate
		notificationSendObject['graceDate'] = graceDate
		notificationSendObject['id'] = notification.id
		notificationSendObject['hasSeen'] = notification.get('hasSeen')

		if notificationType == "beforOccurrence"
			notificationSendObject['type'] = "beforOccurrence"
			promise.resolve(notificationSendObject)
		else if notificationType == "beforeGracePeriod"
			notificationSendObject['type'] = "beforeGracePeriod"
			promise.resolve(notificationSendObject)
		else if notificationType == "missedOccurrence"
			notificationSendObject['type'] = "missedOccurrence"
			promise.resolve(notificationSendObject)
		else
			promise.resolve(notificationSendObject)
	, (error) ->
		promise.reject error
	promise



Parse.Cloud.define "hasSeenNotification", (request, response) ->
	notificationId = request.params.notificationId
	hasSeenNotification(notificationId)
	.then (notification) ->
		response.success "hasSeen"
	, (error) ->
		response.error error

hasSeenNotification = (notificationId) ->
	promise = new Parse.Promise()
	notificationQuery = new Parse.Query('Notification')
	notificationQuery.get(notificationId)
	.then (notification) ->
		notification.set 'hasSeen', true
		notification.save()
		.then (notification) ->
			promise.resolve(notification)
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise




Parse.Cloud.define 'timeZoneConverter', (request, response) ->
	timeZoneConverter('cb11e368-67d6-4df4-b79e-0be25cfe5577','2016-02-01T08:11:44.000Z')
	.then (time) ->
		response.success time
	, (error) ->
		response.error error


timeZoneConverter = (installationId,occurrenceDate) ->
	momenttimezone.tz.add("Asia/Calcutta|HMT BURT IST IST|-5R.k -6u -5u -6u|01232|-18LFR.k 1unn.k HB0 7zX0")
	momenttimezone.tz.link("Asia/Calcutta|Asia/Kolkata")
	convertedTimezoneObject = {}
	promise = new Parse.Promise()
	installationQuery = new Parse.Query(Parse.Installation)
	installationQuery.equalTo('installationId', installationId)
	installationQuery.first(useMasterKey: true)
	.then (installationObj) ->
		if !_.isEmpty(installationObj)
			console.log "******converted******"
			timeZone = installationObj.get("timeZone")
			convertedTime = momenttimezone.tz(occurrenceDate, timeZone).format('DD-MM-YYYY HH:mm')
			console.log installationId
			console.log convertedTime
			console.log "******converted******"

			convertedTimezoneObject['occurrenceDate'] = convertedTime
			convertedTimezoneObject['timeZone'] = timeZone
			promise.resolve convertedTimezoneObject
		else
			# no timezone data found for this user
			console.log "******Not converted******"
			convertedTimezoneObject['occurrenceDate'] = occurrenceDate
			convertedTimezoneObject['timeZone'] = ''
			promise.resolve convertedTimezoneObject
				
		#promise.resolve (convertToZone( new Date(),installationObj['timeZone']).format())
	, (error) ->
		promise.reject error
	promise


convertToZone = (timeObj, timezone) ->
	console.log "****timeObj****"
	console.log timeObj
	convertedTime = momenttimezone.tz(timeObj, timezone)
	console.log convertedTime
	convertedTime




###
createMissedResponse = () ->
	promise = new Parse.Promise()
	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.exists('patient')
	scheduleQuery.include('questionnaire')
	scheduleQuery.find()
	.then (scheduleObjs) ->
		updateMissedResponse = ->
			promise1 = []
			_.each scheduleObjs, (scheduleObj) ->
				timeObj = getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence'))
				currentDate = new Date()
				if currentDate.getTime() > timeObj['upperLimit'].getTime()
					createResponse(scheduleObj.get('questionnaire').id, scheduleObj.get('patient'), scheduleObj)
					#.then (responseObj) ->
					responseObj = new Parse.Object "Response"
					responseObj.set 'patient', scheduleObj.get('patient')
					responseObj.set 'hospital', scheduleObj.get('questionnaire').get('hospital')
					responseObj.set 'project', scheduleObj.get('questionnaire').get('project')
					responseObj.set 'questionnaire', scheduleObj.get('questionnaire')
					responseObj.set 'schedule', scheduleObj
					responseObj.set 'occurrenceDate', scheduleObj.get('nextOccurrence')
					responseObj.set 'status', 'missed'
					responseObj.save()
					.then (responseObj) ->
						console.log "-------------------------------"
						console.log  responseObj.id
						console.log scheduleObj.id
						console.log "-----------------------------"
						scheduleQuery = new Parse.Query('Schedule')
						scheduleQuery.doesNotExist('patient')
						scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'))
						scheduleQuery.first()
						.then (scheduleQuestionnaireObj) ->
							newNextOccurrence = new Date (scheduleObj.get('nextOccurrence').getTime())
							newNextOccurrence.setTime(newNextOccurrence.getTime() + Number(scheduleQuestionnaireObj.get('frequency')) * 1000)
							console.log "-------------"
							console.log "------#{scheduleObj.get('nextOccurrence')}"
							console.log "======#{newNextOccurrence}"
							console.log "-------------"
							scheduleObj.set 'nextOccurrence', newNextOccurrence
							promise1.push(scheduleObj.save())
						#, (error) ->
							#	promise1.reject error
						#, (error) ->
						#	promise1.reject (error)
					#, (error) ->
					#	promise1.reject error
			Parse.Promise.when(promise1)
		updateMissedResponse()
		.then () ->
			promise.resolve("done")
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise







Parse.Cloud.define "createMissedResponse1", (request, response) ->
	getNotifications()
	.then (notifications) ->
		console.log  "done1"
		sendNotifications() 
		.then (notifications) ->
			console.log  "done2"
			checkMissedResponses()
			.then (result) ->
				console.log  "done3"
				createMissedResponse()
				.then (responses) ->
					console.log  (new Date())
					response.success "job_run"
				, (error) ->
					response.error error
			, (error) ->
				promise.reject error				
		, (error) ->
			response.error error
	, (error) ->
		response.error error







createMissedResponse1 = () ->
	promise = new Parse.Promise()
	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.exists("patient")
	scheduleQuery.include("questionnaire")
	scheduleQuery.find()
	.then (scheduleObjects) ->
		result ={}
		responseSaveArr =[]
		scheduleSaveArr =[]
		_.each scheduleObjects, (scheduleObject) ->
			questionnaire = scheduleObject.get("questionnaire")
			patient = scheduleObject.get("patient")
			gracePeriod = questionnaire.get("gracePeriod")
			nextOccurrence =  moment(scheduleObject.get("nextOccurrence"))
			newDateTime = moment(nextOccurrence).add(gracePeriod, 's')
			currentDateTime = moment()
			diffrence = moment(newDateTime).diff(currentDateTime)
			diffrence2 = moment(currentDateTime).diff(newDateTime)
			if(parseInt(diffrence2) > 1)
				responseQuery = new Parse.Query('Response')
				responseQuery.equalTo('questionnaire', scheduleObject.get('questionnaire'))
				responseQuery.equalTo('patient', scheduleObject.get('patient'))
				responseQuery.descending('occurrenceDate')
				responseQuery.notEqualTo('status', 'base_line')
				responseQuery.find()
				.then (responseObjs) ->
					responseData=
						patient: patient
						questionnaire: questionnaire
						status : 'missed'
						schedule : scheduleObject
						sequenceNumber : responseObjs.length + 1
					Response = Parse.Object.extend("Response") 
					responseObj = new Response responseData
					responseSaveArr.push(responseObj)
					getQuestionnaireFrequency(questionnaire)
					.then (frequency) ->
						frequency = parseInt frequency
						newNextOccurrence = moment(nextOccurrence).add(frequency, 's')
						date = new Date(newNextOccurrence)
						scheduleObject.set('nextOccurrence',date)
						scheduleSaveArr.push(scheduleObject)
					, (error) ->
						promise.reject error
				, (error) ->
					promise.reject error
		# save all responses
		Parse.Object.saveAll responseSaveArr
			.then (resObjs) ->
				# update all schedule nextoccurrence
				Parse.Object.saveAll scheduleSaveArr
					.then (scheduleObjs) ->
						promise.resolve scheduleObjs
					, (error) ->
						promise.reject (error)   
			, (error) ->
				promise.reject (error)
	, (error) ->
		promise.reject error

	promise


###





###
console.log "======================================="
	console.log "nextOccurrence = #{nextOccurrence}"
	console.log "beforeReminder =#{beforeReminder}"
	console.log "afterReminder =#{afterReminder}"
	console.log "currentDate = #{currentDate}"
	console.log "graceDate = #{graceDate}"
	console.log "rem = #{reminderTime}"
	console.log "=============================================="
###