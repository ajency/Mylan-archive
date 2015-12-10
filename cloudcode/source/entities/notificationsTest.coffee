Parse.Cloud.define "pushNotification", (request, response) ->
	installationQuery = new Parse.Query(Parse.Installation)
	installationQuery.equalTo('installationId',request.params.installationId)
	Parse.Push.send({
		where: installationQuery
		data : {alert:"First push message :-)"}
		success: () -> response.success "Message pushed"
		error: (error) -> response.error error})

