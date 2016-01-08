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
					notificationType = getNotificationType(scheduleObj)
					if notificationType != ""
						notificationObj = new Parse.Object('Notification')
						notificationObj.set 'hasSeen', false
						notificationObj.set 'patient', scheduleObj.get('patient')
						notificationObj.set 'type',	notificationType
						notificationObj.set 'processed', false
						notificationObj.save()
						.then (notificationObj) ->
							promise.resolve()
						, (error) ->
							promise.reject error
					else
						promise1
				, (error) ->
					promise.reject error
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
	
	if (currentDate.getTime() == (nextOccurrence.getTime() - (reminderTime * 1000)))
	#if (currentDate.getTime() >= (nextOccurrence.getTime() - (reminderTime * 1000))) and (currentDate.getTime() <= nextOccurrence.getTime())
		"beforOccurrence"
	else if (currentDate.getTime() == (graceDate.getTime() - (reminderTime * 1000)))
	#if (currentDate.getTime() >= (graceDate.getTime() - (reminderTime * 1000))) and (currentDate.getTime() <= graceDate.getTime())
			"beforeGracePeriod"
	else if currentDate.getTime() == graceDate.getTime()
	#else if currentDate.getTime() >= graceDate.getTime()
			"missedOccurrence"
	else
		""

getNotificationMessage = (scheduleObj, notificationType) ->
	nextOccurrence = scheduleObj.get('nextOccurrence')
	graceDate =new Date(scheduleObj.get('nextOccurrence').getTime() + (scheduleObj.get('questionnaire').get('gracePeriod') * 1000))

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
											installationQuery.limit(1)
											installationQuery.find()
											Parse.Push.send({
												where: installationQuery
												data: {alert: getNotificationMessage(scheduleObj, notification.get('type'))}
											})
										,(error) ->
											promise.reject error
									promise2
								sendToInstallations()
							, (error) ->
								promise1.reject error
						, (error) ->
							promise1.reject error
					, (error) ->
						promise1.reject error
				, (error) ->
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



createMissedResponse = () ->
    promise = new Parse.Promise()
    scheduleQuery = new Parse.Query('Schedule')
    scheduleQuery.exists("patient")
    scheduleQuery.include("questionnaire")
    scheduleQuery.find()
    .then (scheduleObjects) ->
        result ={}
        responseSaveArr =[]
        scheduleSaveArr =[]
        _.each scheduleObjects , (scheduleObject) ->
            questionnaire = scheduleObject.get("questionnaire")
            patient = scheduleObject.get("patient")
            gracePeriod = questionnaire.get("gracePeriod")
            nextOccurrence =  moment(scheduleObject.get("nextOccurrence"))
            newDateTime = moment(nextOccurrence).add(gracePeriod, 's')
            currentDateTime = moment()
 
            diffrence = moment(newDateTime).diff(currentDateTime)
            diffrence2 = moment(currentDateTime).diff(newDateTime)
            console.log newDateTime
            console.log currentDateTime
            console.log diffrence
            console.log diffrence2
            if(parseInt(diffrence2) > 1)
                responseData=
                    patient: patient
                    questionnaire: questionnaire
                    status : 'missed'
                    schedule : scheduleObject

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
	getNotifications()
	.then (notifications) ->
		sendNotifications() 
		.then (notifications) ->
			createMissedResponse()
			.then (responses) ->
				response.success "job_run"
			, (error) ->
				response.error error				
		, (error) ->
			response.error error
	, (error) ->
		response.error error


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