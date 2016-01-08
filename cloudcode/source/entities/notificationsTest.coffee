Parse.Cloud.define "pushNotification", (request, response) ->
	installationQuery = new Parse.Query(Parse.Installation)
	installationQuery.equalTo('installationId',request.params.installationId)
	Parse.Push.send({
		where: installationQuery
		data : {alert:"First push message :-)"}
		success: () -> response.success "Message pushed"
		error: (error) -> response.error error})



###
sendNotifications = () ->
	promise = new Parse.Promise()
	notificationQuery = new Parse.Query('Notification')
	notificationQuery.equalTo('processed', false)
	notificationQuery.find()
	.then (notifyObjs) ->
		promise1 = Parse.Promise.as()
		_.each notifyObjs, (notifyObj) ->
			promise1 = promise1
			.then () ->
				patientId = notifyObjs.get('patient') 
				tokenStorageQuery = new Parse.Query('TokenStorage')
				tokenStorageQuery.equalTo('referenceCode', patientId)
				tokenStorageQuery.find()
				.then (tokenStorageObjs) ->
					promise2 = Parse.Promise.as()
					_.each tokenStorageObjs, (tokenStorageObj) ->
						promise2 = promise2
						.then () ->
							installationQuery = new Parse.Query('Installation')
							installationQuery.equalTo(tokenStorageObj.get('installationId'))
							Parse.Push.send({
								where: installationQuery
								data: {alert: getNotificationMessage(notifyObj.get('type'))}
								})
							.then () ->
								notifyObj. set 'processed', true
								notifyObj.save()
							, (error) ->
								promise2.reject error
						, (error) ->
							promise2.reject error
					promise2
				, (error)
					promise1.reject error
			, (error) ->
				promise1.reject error
		promise1
	, (error) ->
		promise.reject error
	promise



###