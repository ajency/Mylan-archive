Parse.Cloud.define "importData", (request, response) ->
	results = request.params.results
	saveArr =[]

	_.each results, (result) ->
		Schedule = Parse.Object.extend("Schedule") 
		scheduleObj = new Schedule result
		saveArr.push(scheduleObj)

	Parse.Object.saveAll saveArr
	.then (Objs) -> 
		response.success Objs
	, (error) ->
		response.error error

