Parse.Cloud.define "testNotifications", (request, response) ->
	getNotifications()
	.then (notifications) ->
		sendNotifications() 
		.then (notifications) ->
			response.success notifications
		, (error) ->
			response.error error
	, (error) ->
		response.error error


getNotifications = () ->
	promise = new Parse.Promise()

	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.exists('patient')
	#scheduleQuery.equalTo('patient', '00011121')
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
						notificationType = getNotificationType(scheduleObj)
						if notificationType != ""
							notificationObj = new Parse.Object('Notification')
							notificationObj.set 'hasSeen', false
							notificationObj.set 'patient', scheduleObj.get('patient')
							notificationObj.set 'type',	notificationType
							notificationObj.set 'processed', false
							notificationObj.set 'schedule', scheduleObj
							notificationObj.save()
						else
							dummy = new Parse.Promise()
							dummy.resolve()
							dummy
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

	nextOccurrence = scheduleObj.get('nextOccurrence')
	currentDate = new Date()
	graceDate =new Date(scheduleObj.get('nextOccurrence').getTime() + (scheduleObj.get('questionnaire').get('gracePeriod') * 1000))
	reminderTime = scheduleObj.get('questionnaire').get('reminderTime')
	beforeReminder = new Date(nextOccurrence.getTime() - reminderTime * 1000)
	afterReminder =  new Date(nextOccurrence.getTime() + reminderTime * 1000)
	
	#if (currentDate.getTime() == (nextOccurrence.getTime() - (reminderTime * 1000)))
	if (currentDate.getTime() >= (nextOccurrence.getTime() - (reminderTime * 1000) - (40 * 1000))) and (currentDate.getTime() <= (nextOccurrence.getTime() - (reminderTime * 1000) + (40 * 1000)))
		"beforOccurrence"
	#else if (currentDate.getTime() == (graceDate.getTime() - (reminderTime * 1000)))
	else if (currentDate.getTime() >= (graceDate.getTime() - (reminderTime * 1000) - (40 * 1000))) and (currentDate.getTime() <= (graceDate.getTime() - (reminderTime * 1000) + (40 * 1000)))
			"beforeGracePeriod"
	#else if currentDate.getTime() >= graceDate.getTime()
	else if currentDate.getTime() >= graceDate.getTime()
			"missedOccurrence"
	else
		""

getNotificationMessage = (scheduleObj, notificationType) ->
	nextOccurrence = scheduleObj.get('nextOccurrence')
	questionnaireObj = scheduleObj.get('questionnaire') 

	graceDate =new Date(scheduleObj.get('nextOccurrence').getTime() + (scheduleObj.get('questionnaire').get('gracePeriod') * 1000))
	console.log "-=-=-=-=-=-=-"
	console.log "scheduleObj"
	console.log scheduleObj
	#console.log scheduleObj.get('questionnaire').get('gracePeriod')
	console.log "-=-=-=-=-=-=-"

	if notificationType == "beforOccurrence"
		"Questionnaire is due on #{nextOccurrence}"
	else if notificationType == "beforeGracePeriod"
		"Questionnairre was due on #{nextOccurrence}. Please submit it by #{graceDate}"
	else if notificationType == "missedOccurrence"
		"You have missed the questionnaire due on #{nextOccurrence}"
	else
		""

sendNotifications = () ->
	Arr = []
	promise = new Parse.Promise()
	notificationQuery = new Parse.Query('Notification')
	notificationQuery.equalTo('processed', false)
	notificationQuery.find()
	.then (notifications) ->
		getNotifications = ->
			promise1 = Parse.Promise.as()
			_.each notifications, (notification) ->
				promise1 = promise1
				.then () ->
					notification.set 'processed', true
					notification.save()
					.then (notification) ->
						scheduleQuery = new Parse.Query('Schedule')
						scheduleQuery.equalTo('patient', notification.get('patient'))
						scheduleQuery.first()
						.then (scheduleObj) ->
							tokenStorageQuery = new Parse.Query('TokenStorage')
							tokenStorageQuery.equalTo('referenceCode', notification.get('patient'))
							tokenStorageQuery.find(useMasterKey: true)
							.then (tokenStorageObjs) ->
								sendToInstallations = ->
									promise2 = Parse.Promise.as()
									_.each tokenStorageObjs, (tokenStorageObj) ->
										promise2 = promise2
										.then () ->
											installationQuery = new Parse.Query('Installation')
											installationQuery.equalTo('installationId', tokenStorageObj.get('installationId'))
											#installationQuery.equalTo('installationId', '4975e846-af7a-4113-b0c4-c73117908ef7')
											installationQuery.limit(1)
											installationQuery.find()
											Parse.Push.send({
												where: installationQuery
												data: {alert: getNotificationMessage(scheduleObj, notification.get('type'))}
											})
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

###	#responseQuery = new Parse.Query('Response')
	#res
	createMissedResponse()
	.then (result) ->
		response.success result
	, (error) ->
		response.error error
###
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
###


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





Parse.Cloud.job 'commonJob', (request, response) ->
	checkMissedResponses()
	.then (result) ->
		createMissedResponse()
		.then (responses) ->
			getNotifications()
			.then (notifications) ->
				sendNotifications() 
				.then (notifications) ->
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