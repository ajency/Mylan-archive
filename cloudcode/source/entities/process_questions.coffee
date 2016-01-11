Parse.Cloud.define "startQuestionnaire", (request, response) ->
#	if !request.user
#		response.error('Must be logged in.')
#	else
	responseId = request.params.responseId
	questionnaireId = request.params.questionnaireId
	patientId = request.params.patientId

	if (responseId != "") and (!_.isUndefined responseId) and (!_.isUndefined questionnaireId) and (!_.isUndefined patientId)
		
		responseQuery = new Parse.Query("Response")
		responseQuery.get(responseId)
		.then (responseObj) ->
			if responseObj.get('status') == 'completed'
				response.error "questionnaire_submitted"

			else
				answeredQuestions = responseObj.get('answeredQuestions')
				questionQuery = new Parse.Query('Questions')
				questionQuery.include('nextQuestion')
				questionQuery.include('previousQuestion')
				questionQuery.include('questionnaire')
				if answeredQuestions.length != 0
					questionQuery.get(answeredQuestions[answeredQuestions.length - 1])
					.then (questionObj) ->
						getNextQuestion(questionObj, [])
						.then (nextQuestionObj) ->

							if !_.isEmpty(nextQuestionObj)
								getQuestionData nextQuestionObj, responseObj, responseObj.get('patient')
								.then (questionData) ->

									response.success questionData
								,(error) ->
									response.error error
							else
								getSummary(responseObj)
								.then (summaryObjects) ->
									result = {}
									result['status'] = "saved_successfully"
									result['summary'] = summaryObjects
									response.success result
								,(error) ->
									response.error error					       
						,(error) ->
							response.error error
					,(error) ->
						response.error error

				else
					firstQuestion questionnaireId
					.then (questionObj) ->
						getQuestionData questionObj, responseObj, patientId
						.then (questionData) ->
							response.success questionData
						,(error) ->
							response.error error	
					,(error) ->
						response.error error

		,(error) ->
			response.error error

	else if  (responseId == "") and (!_.isUndefined questionnaireId) and (!_.isUndefined patientId)
		scheduleQuery = new Parse.Query('Schedule')
		scheduleQuery.equalTo('patient', patientId)
		scheduleQuery.first()
		.then (scheduleObj) ->
			createResponse questionnaireId, patientId, scheduleObj
			.then (responseObj) ->
				responseObj.set 'status', 'started'
				responseObj.set 'occurrenceDate', scheduleObj.get('nextOccurrence')
				responseObj.save()
				.then (responseObj) ->
					scheduleQuery = new Parse.Query('Schedule')
					scheduleQuery.doesNotExist('patient')
					scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'))
					scheduleQuery.first()
					.then (scheduleQuestionnaireObj) ->
						newNextOccurrence = new Date (scheduleObj.get('nextOccurrence').getTime())
						newNextOccurrence.setTime(newNextOccurrence.getTime() + Number(scheduleQuestionnaireObj.get('frequency')) * 1000)
						scheduleObj.set 'nextOccurrence', newNextOccurrence
						scheduleObj.save()
						.then (scheduleObj) ->
							firstQuestion questionnaireId
							.then (questionObj) ->
								getQuestionData questionObj, responseObj, patientId
								.then (questionData) ->
									response.success questionData
								,(error) ->
									response.error error	
							,(error) ->
								response.error error
						,(error) ->
							response.error error
					,(error) ->
						response.error error
				,(error) ->
					response.error error	
			,(error) ->
				response.error error
		,(error) ->
			response.error error

	else
		response.error "Invalid request."






firstQuestion = (questionnaireId) ->
	promise = new Parse.Promise()
	questionnaireQuery = new Parse.Query("Questionnaire")
	questionnaireQuery.get(questionnaireId)
	.then (questionnaireObj) ->
		questionsQuery = new Parse.Query("Questions")
		questionsQuery.equalTo('questionnaire', questionnaireObj)
		questionsQuery.include('questionnaire')
		questionsQuery.find()
		.then (questionsObjs) ->
			checkIfFirstQuestion = (questionObj) ->
				if _.isUndefined(questionObj.get('previousQuestion')) and  not questionObj.get 'isChild'
					true
				else
					false

			checkAll = for questionObj in questionsObjs
						if checkIfFirstQuestion questionObj
							questionObj
						else
							continue
						
			promise.resolve checkAll[0] 
		, (error) ->
			promise.reject error

	, (error) ->
		promise.reject error
	promise



getCurrentAnswer = (questionObj, responseObj) ->
	options = []
	hasAnswer = {}
	promise = new Parse.Promise()

	answerQuery = new Parse.Query "Answer"
	answerQuery.equalTo('response', responseObj)
	answerQuery.equalTo('question', questionObj)
	answerQuery.descending('updatedAt')
	answerQuery.include('question')
	answerQuery.include('option')
	
	
	if questionObj.get('type') == 'multi-choice'
		answerQuery.find()
		.then (answerObjs) ->
			#options.push answerObj.get('option').get('label') for answerObj in answerObjs
			options.push answerObj.get('option').id for answerObj in answerObjs
			
			if (!_.isUndefined(answerObjs[0]))
				hasAnswer['value'] =  answerObjs[0].get('value')
				hasAnswer['date'] = answerObjs[0].get('updatedAt')
				hasAnswer['option'] = options
			
			promise.resolve(hasAnswer)

		, (error) ->
			promise.reject error

	else 
		answerQuery.first()
		.then (answerObj) ->
			if !_.isUndefined(answerObj)
				#options.push(answerObj.get('option').get('label')) if !_.isUndefined(answerObj.get('option'))
				options.push(answerObj.get('option').id) if !_.isUndefined(answerObj.get('option'))
				hasAnswer['option'] = options
				hasAnswer['value'] =  answerObj.get('value')
				hasAnswer['date'] = answerObj.get('updatedAt')
			promise.resolve(hasAnswer)
		, (error) ->
			promise.reject error

	promise



getQuestionData = (questionObj, responseObj, patientId) ->
	promise = new Parse.Promise()
	questionObj.fetch()
	.then ->
		questionData = {}
		questionData['responseId'] = responseObj.id
		questionData['responseStatus'] = responseObj.get('status')
		questionData['questionId'] = questionObj.id
		questionData['questionType'] = questionObj.get('type')
		questionData['question'] = questionObj.get('question')
		questionData['next'] =  if (!_.isUndefined(questionObj.get('nextQuestion'))) then true else false
		questionData['previous'] =  if (!_.isUndefined(questionObj.get('previousQuestion'))) then true else false
		questionData['options'] = []
		questionData['hasAnswer'] = {}
		questionData['previousQuestionnaireAnswer'] = {}
		questionData['questionTitle'] = questionObj.get('title')
		questionData['editable'] = {}

		editable = questionObj.get('questionnaire')
#		console.log editable
		editable.fetch()
		.then ->
			questionData['editable'] = editable.get('editable')

			getPreviousQuestionnaireAnswer(questionObj, responseObj, patientId)
			.then (previousQuestionnaireAnswer) ->
				questionData['previousQuestionnaireAnswer'] = previousQuestionnaireAnswer
				getCurrentAnswer(questionObj, responseObj)
				.then (hasAnswer) ->
					questionData['hasAnswer'] = hasAnswer
					if questionObj.get('type') == 'single-choice' or questionObj.get('type') == 'multi-choice' or questionObj.get('type') == 'input'
						optionsQuery = new Parse.Query "Options"
						optionsQuery.equalTo('question', questionObj)
						optionsQuery.find()
						.then (optionObjs) ->
							options = []
							for option in optionObjs 
								optionObj = {} 			
								optionObj['id'] = option.id
								optionObj['option'] = option.get('label')
								optionObj['score'] = option.get('score')
								options.push(optionObj)
							questionData['options'] = options
							promise.resolve(questionData)	

						, (error) ->
							promise.reject error
					else
						promise.resolve questionData
				, (error) ->
					promise.reject error
			, (error) ->
				promise.reject error
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise	


Parse.Cloud.define 'getNextQuestion', (request, response) ->
#	if !request.user
#		response.error('Must be logged in.')
#	else
	responseId = request.params.responseId
	questionId = request.params.questionId
	options = request.params.options
	value = request.params.value

	responseQuery = new Parse.Query('Response')
	responseQuery.get(responseId)
	.then (responseObj) ->
		if responseObj.get('status') == 'completed'
			response.error "questionnaire_submitted."
		else 
			questionQuery = new Parse.Query('Questions')
			questionQuery.include('nextQuestion')
			questionQuery.include('previousQuestion')
			questionQuery.include('questionnaire')
			questionQuery.get(questionId)

			.then (questionObj) ->
				saveAnswer responseObj, questionObj, options, value
				.then (answers) ->

					getNextQuestion(questionObj, options)
					.then (nextQuestionObj) ->

						if !_.isEmpty(nextQuestionObj)
							getQuestionData nextQuestionObj, responseObj, responseObj.get('patient')
							.then (questionData) ->

								response.success questionData
							,(error) ->
								response.error error
						else
							getSummary(responseObj)
							.then (summaryObjects) ->
								result = {}
								result['status'] = "saved_successfully"
								result['summary'] = summaryObjects
								response.success result
							,(error) ->
								response.error error					       
					,(error) ->
						response.error error
				,(error) ->
					response.error error
			,(error) ->
				response.error error
	,(error) ->
		response.error error


getNextQuestion = (questionObj, option) ->
	promise = new Parse.Promise()

	getRequiredQuestion = () ->
		if !_.isUndefined questionObj.get('nextQuestion')
			promise.resolve(questionObj.get('nextQuestion'))

		else if (_.isUndefined questionObj.get('nextQuestion')) and !questionObj.get('isChild')
#			console.log "=========================================="
#			console.log questionObj.get('isChild')
#			console.log 
			promise.resolve({})

		else

			while questionObj.get('isChild')
				questionObj = questionObj.get('previousQuestion')

			questionObj.fetch()
			.then ->
#				console.log ".........................................."
#				console.log questionObj
				if !_.isUndefined questionObj.get('nextQuestion')
#					console.log "*****************************************"
#					console.log questionObj.get('nextQuestion').id
					promise.resolve(questionObj.get('nextQuestion'))
				else
#					console.log questionObj.id
#					console.log "xxxxxxxxxxxxxxxxxxxxxxx"
					promise.resolve({})
			, (error) ->
				promise.error error



	if questionObj.get('type') == 'single-choice' and (!_.isUndefined(questionObj.get('condition')))
		optionsQuery = new Parse.Query "Options"
		optionsQuery.get(option[0])
		.then (optionObj) ->

			conditions = questionObj.get('condition')
			conditionalQuestion = ( condition['questionId'] for condition in conditions when condition['optionId'] == optionObj.id)
			if conditionalQuestion.length != 0
				questionQuery = new Parse.Query("Questions")
				questionQuery.include('questionnaire')
				questionQuery.get(conditionalQuestion[0])
				.then (optionQuestionObj) ->
					promise.resolve(optionQuestionObj)

				,(error) ->
					promise.error error
			else

				getRequiredQuestion()


		,(error) ->
			promise.error error

	else
		getRequiredQuestion()

	promise







getLastQuestion = (questionnaireObj) ->
	promise = new Parse.Promise()
	questionQuery = new Parse.Query('Questions')
	questionQuery.equalTo('questionnaire', questionnaireObj)
	questionQuery.find()
	.then (questionObjects) ->
		lastQuestion = ""
		for questionObj in questionObjects
			if !questionObj.get('isChild') and _.isUndefined(questionObj.get('nextQuestion'))
				lastQuestion = questionObj
		promise.resolve lastQuestion
	, (error) ->
		promise.error error
	promise


Parse.Cloud.define "getPreviousQuestion", (request, response) ->
#	if !request.user
#		response.error('Must be logged in.')
#
#	else
	responseId = request.params.responseId
	last = questionId = request.params.questionId
	options = request.params.options
	value = request.params.value

	responseQuery = new Parse.Query('Response')
	responseQuery.include('questionnaire')
	responseQuery.get(responseId)
	.then (responseObj) ->
		if responseObj.get('status') == 'Completed'
			response.error "questionnaire_submitted."

		else
			getLastQuestion(responseObj.get('questionnaire'))
			.then (lastQuestion) ->
				if questionId == ""
					questionId = lastQuestion.id
				questionQuery = new Parse.Query('Questions')
				questionQuery.include('previousQuestion')
				questionQuery.include('questionnaire')
				questionQuery.get(questionId)
				.then (questionObj) ->
					if !_.isEmpty(options) or value != ""
						saveAnswer responseObj, questionObj, options, value
							.then (answersArray) ->
								if _.isUndefined(questionObj.get('previousQuestion')) and  not questionObj.get 'isChild'
									getQuestionData questionObj, responseObj, responseObj.get('patient')
									.then (questionData) ->
										response.success questionData
									,(error) ->
										response.error error

								else
									getQuestionData questionObj.get('previousQuestion'), responseObj, responseObj.get('patient')
									.then (questionData) ->
										response.success questionData
									,(error) ->
										response.error error
							,(error) ->
								response.error error										
					else
						console.log last
						if _.isUndefined(questionObj.get('previousQuestion')) and  not questionObj.get 'isChild'
							getQuestionData questionObj, responseObj, responseObj.get('patient')
							.then (questionData) ->
								response.success questionData
							,(error) ->
								response.error error

						else if last == ""
							getQuestionData questionObj, responseObj, responseObj.get('patient')
							.then (questionData) ->
								response.success questionData
							,(error) ->
								response.error error

						else
							getQuestionData questionObj.get('previousQuestion'), responseObj, responseObj.get('patient')
							.then (questionData) ->
								response.success questionData
							,(error) ->
								response.error error
				,(error) ->
					response.error error
			,(error) ->
				response.error error
	,(error) ->
		response.error error





Parse.Cloud.define 'getSummary', (request, response) ->
#	if !request.user
#		response.error('Must be logged in.')
#
#	else
	responseId = request.params.responseId
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo("objectId", responseId)
	responseQuery.first()
	.then (responseObj) ->
		getSummary responseObj
		.then (answerObjects) ->
			#result = {}
			#result['answerObjects'] = answerObjects
			#result['submissionDate'] = responseObj.updatedAt
			#result['sequenceNumber'] = responseObj.get('sequenceNumber')
			#response.success result
			response.success answerObjects
		, (error) ->
			response.error error
	, (error) ->
		response.error error

getSummary = (responseObj) ->
	promise = new Parse.Promise()

	answerQuery = new Parse.Query('Answer')
	answerQuery.include("question")
	answerQuery.include("option")
	answerQuery.ascending('createdAt')
	answerQuery.equalTo("response", responseObj)
	answerQuery.find()
	.then (answerObjects) ->
		promise.resolve getAnswers answerObjects
	, (error) ->
		promise.error error
	promise





getAnswers = (answerObjects) ->

	results = (answerObj) ->
		input:
			answerObj['answer']
		question:
			answerObj['question'].get('question')
		optionSelected:
			answerObj['optionsSelected']
		val:
			answerObj['temp']

	answers = []
	getUniqueQuestions = (answerObj) ->
		currentQuestion = answerObj.get('question')
		questions = (obj['question'] for obj in answers)

		answer = {}
		if currentQuestion.id != (q.id for q in questions when q.id == currentQuestion.id)[0]
			answer['temp'] = (q for q in questions when q.id == currentQuestion.id)[0]
			answer["question"] = currentQuestion
			answer["answer"] = answerObj.get('value') 
			if currentQuestion.get('type') == 'multi-choice' 
				answer['optionsSelected'] = []
				answer['optionsSelected'].push(answerObj.get('option').get('label'))
			else
				answer['optionsSelected'] = []
				if !_.isUndefined(answerObj.get('option'))
					answer['optionsSelected'].push(answerObj.get('option').get('label'))
			answers.push(answer)
		else if currentQuestion.get('type') == 'multi-choice'
			index = (i for q,i in questions when currentQuestion.id == q.id)[0]
			answers[index]['optionsSelected'].push(answerObj.get('option').get('label'))
    
	getUniqueQuestions answerObj for answerObj in answerObjects
	results answerObj for answerObj in answers   




Parse.Cloud.define "dashboard2", (request, response) ->
	patientId = request.params.patientId
#	results = {}
	results = []

	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.equalTo('patient', patientId)
	scheduleQuery.include('questionnaire')
	scheduleQuery.first()
	.then (scheduleObj) ->
		getResumeObject(scheduleObj, patientId)
		.then (resumeObj) ->
			getStartObject(scheduleObj, patientId)
			.then (startObj) ->
				getUpcomingObject(scheduleObj, patientId)
				.then (upcomingObj) ->
					getCompletedObjects(scheduleObj, patientId)
					.then (completedObj) ->
						getMissedObjects(scheduleObj, patientId)
						.then (missedObj) ->
							results.push(resumeObj)
							results.push(startObj)
							results.push(upcomingObj)
							results.push(completedObj)
							results.push(missedObj)

#							results['resume'] = resumeObj
#							results['start'] = startObj
#							results['upcoming'] = upcomingObj
#							results['completed'] = completedObj
#							results['missed'] = missedObj
							response.success results
						, (error) ->
							response.error error				
					, (error) ->
						response.error error				
				, (error) ->
					response.error error				
			, (error) ->
				response.error error				
		, (error) ->
			response.error error
	, (error) ->
		response.error error 






getStartObject = (scheduleObj, patientId) ->
	startObj = {}
	promise = new Parse.Promise()

	timeObj = getValidPeriod(scheduleObj)

	if isValidTime(timeObj) 
		responseQuery1 = new Parse.Query('Response')
		responseQuery1.equalTo('status', 'started')

		responseQuery2 = new Parse.Query('Response')
		responseQuery2.equalTo('status', 'completed')

		responseQuery = Parse.Query.or(responseQuery1, responseQuery2)
		responseQuery.equalTo('patient', patientId)
		responseQuery.greaterThanOrEqualTo('createdAt', timeObj['lowerLimit'])
		responseQuery.lessThanOrEqualTo('createdAt', timeObj['upperLimit'])

		responseQuery.first()
		.then (responseObj) ->
			if !_.isUndefined(responseObj)
				startObj['status'] = "not_start"
				#startObj['responseId'] = ""
				startObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
			else
				startObj['status'] = "start"
				#startObj['responseId'] = ""
				startObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')

			promise.resolve(startObj)
		, (error) ->
			promise.error error
	else
		startObj['status'] = "not_start"
		startObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
		#startObj['responseId'] = ""
		promise.resolve(startObj)

	promise



getUpcomingObject = (scheduleObj, patientId) ->
	upcomingObj = {}
	promise = new Parse.Promise()

	timeObj = getValidPeriod(scheduleObj)

	if isValidUpcomingTime(timeObj) 
		upcomingObj['status'] = "upcoming"
		upcomingObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
		#upcomingObj['responseId'] = ""
		promise.resolve(upcomingObj)
	else
		upcomingObj['status'] = "not_upcoming"
		upcomingObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')

		#upcomingObj['responseId'] = ""
		promise.resolve(upcomingObj)
	promise

isValidUpcomingTime = (timeObj) ->
	currentTime = new Date()

	if timeObj['lowerLimit'].getTime() > currentTime.getTime()
		true
	else
		false



getCompletedObjects = (scheduleObj, patientId) ->
	completedObj = {}
	promise = new Parse.Promise()
	
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('patient', patientId)
	responseQuery.equalTo('status', 'completed')
	responseQuery.descending('createdAt')
	responseQuery.find()
	.then (responseObjs) ->
		completedObj['status'] = "completed"
		completedObj['responseIds'] = (responseObj.id for responseObj in responseObjs)
		completedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
		promise.resolve(completedObj)
	, (error) ->
		promise.error error
	promise






getResumeObject = (scheduleObj, patientId) ->
	resumeObj = {}
	promise = new Parse.Promise()
	
	timeObj = getValidPeriod(scheduleObj)

	if isValidTime(timeObj) 
		responseQuery = new Parse.Query('Response')
		responseQuery.equalTo('patient', patientId)
		responseQuery.equalTo('status', 'started')
		responseQuery.greaterThanOrEqualTo('createdAt', timeObj['lowerLimit'])
		responseQuery.lessThanOrEqualTo('createdAt', timeObj['upperLimit'])
		responseQuery.first()
		.then (responseObj) ->
			if !_.isUndefined(responseObj)
				resumeObj['status'] = "resume"
				resumeObj['responseId'] = responseObj.id
				resumeObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')

			else
				resumeObj['status'] = "not_resume"
				resumeObj['responseId'] = ""
				resumeObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')

			promise.resolve(resumeObj)
		, (error) ->
			promise.error error
	else
		resumeObj['status'] = "not_resume"
		resumeObj['responseId'] = ""
		resumeObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')

		promise.resolve(resumeObj)

	promise




# getMissedObjects = (scheduleObj, patientId) ->
# 	missedObj = {}
# 	promise = new Parse.Promise()

# 	timeObj = getValidPeriod(scheduleObj)

# 	if !isValidMissedTime(timeObj) 
# 		missedObj['status'] = "not_missed"
# 		missedObj['responseId'] = ""
# 		missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')

# 		promise.resolve(missedObj)

# 	else 
# 		responseQuery = new Parse.Query('Response')
# 		responseQuery.equalTo('patient', patientId)
# 		responseQuery.greaterThanOrEqualTo('createdAt', timeObj['lowerLimit'])
# 		#responseQuery.lessThanOrEqualTo('createdAt', timeObj['upperLimit'])
# 		responseQuery.first()
# 		.then (responseObj) ->
# 			if !_.isUndefined(responseObj) and responseObj.get('status') == 'completed'
# 				missedObj['status'] = "not_missed"
# 				missedObj['responseId'] = ""
# 				missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
# 				promise.resolve(missedObj)

# 			else if !_.isUndefined(responseObj) and responseObj.get('status') == 'missed'
# 				missedObj['status'] = "missed"
# 				missedObj['responseId'] = responseObj.id
# 				missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
# 				promise.resolve(missedObj)

# 			else if !_.isUndefined(responseObj) and responseObj.get('status') == 'started'
# 				responseObj.set('status', 'missed')
# 				responseObj.save()
# 				.then (responseObj) ->
# 					missedObj['status'] = "missed"
# 					missedObj['responseId'] = responseObj.id
# 					missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
# 					promise.resolve(missedObj)
# 				, (error) ->
# 					promise.error error
# 			else if _.isUndefined(responseObj)
# 				createResponse(scheduleObj.get('questionnaire').id, patientId)
# 				.then (responseObj) ->
# 					responseObj.set('status', 'missed')
# 					responseObj.save()
# 					.then (responseObj) ->
# 						missedObj['status'] = "missed"
# 						missedObj['responseId'] = responseObj.id
# 						missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
# 						promise.resolve(missedObj)
# 					, (error) ->
# 						promise.error error
# 				, (error) ->
# 					promise.error error
# 		, (error) ->
# 			promise.error error
# 	promise


getMissedObjects = (scheduleObj, patientId) ->
	missedObj = {}
	promise = new Parse.Promise()

	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('patient', patientId)
	#responseQuery.greaterThanOrEqualTo('createdAt', timeObj['lowerLimit'])
	#responseQuery.lessThanOrEqualTo('createdAt', timeObj['upperLimit'])
	responseQuery.equalTo('status', 'missed')
	responseQuery.descending('createdAt')
	responseQuery.first()
	.then (responseObj) ->
		if !_.isUndefined(responseObj) and responseObj.get('status') == 'missed'
			missedObj['status'] = "missed"
			#missedObj['responseId'] = responseObj
			missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
			promise.resolve(missedObj)
		else
			missedObj['status'] = "not_missed"
			#missedObj['responseId'] = ""
			missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence')
			promise.resolve(missedObj)
	, (error) ->
		promise.error error
	promise


	


Parse.Cloud.define "dashboard1", (request, response) ->
	patientId = request.params.patientId
	results = []

	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.equalTo('patient', patientId)
	scheduleQuery.include('questionnaire')
	scheduleQuery.first()
	.then (scheduleObj) ->
		responseQuery = new Parse.Query('Response')
		responseQuery.equalTo('patient', patientId)
		responseQuery.descending('createdAt')
		responseQuery.find()
		.then (responseObjs) ->
			timeObj = getValidPeriod(scheduleObj)
			status =""
			if isValidTime(timeObj)
				status ="Start"
			else if isValidUpcomingTime(timeObj)
				status = "Upcoming"

			

			upcoming_due =
				date: scheduleObj.get('nextOccurrence')
				status: status 

			results.push(upcoming_due)
			for responseObj in responseObjs
				result = {}
				result['status'] = responseObj.get('status')
				result['date'] = responseObj.createdAt
				result['responseId'] = responseObj.id
				results.push(result)

			response.success(results)
		, (error) ->
			response.error error 
	, (error) ->
		response.error error


Parse.Cloud.define "updateMissedObjects", (request, response) ->
	patientId = request.params.patientId
	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.equalTo('patient', patientId)
	scheduleQuery.include('questionnaire')
	scheduleQuery.first()
	.then (scheduleObj) ->
		updateMissedObjects scheduleObj, patientId
		.then () ->
			response.success scheduleObj
		, (error) ->
			promise.error error
	, (error) ->
		promise.error error




Parse.Cloud.define "dashboard", (request, response) ->
	console.log "---------------------------"
	console.log request
	console.log "-------------------------------"
#	if !request.user
#		response.error('Must be logged in.')
#	else
	results = []
	patientId = request.params.patientId
	scheduleQuery = new Parse.Query('Schedule')
	scheduleQuery.include('questionnaire')
	scheduleQuery.equalTo('patient', patientId)
	scheduleQuery.first()
	.then (scheduleObj) ->
		updateMissedObjects scheduleObj, patientId
		.then () ->
			responseQuery = new Parse.Query('Response')
			responseQuery.equalTo('patient', patientId)
			responseQuery.descending('occurrenceDate')
			responseQuery.find()
			.then (responseObjs) ->
				timeObj = getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence'))
				status =""
				if isValidTime(timeObj)
					status ="due"
				else if isValidUpcomingTime(timeObj)
					status = "upcoming"
				upcoming_due =
					occurrenceDate: scheduleObj.get('nextOccurrence')
					status: status 
				results.push(upcoming_due)
				for responseObj in responseObjs
					result = {}
					result['status'] = responseObj.get('status')
					result['occurrenceDate'] = responseObj.get('occurrenceDate')
					result['occurrenceId'] = responseObj.id
					results.push result
				response.success (results)
				#response.success (timeObj)
			, (error) ->
				response.error error 
		, (error) ->
			response.error error 
	, (error) ->
		response.error error 





updateMissedObjects = (scheduleObj, patientId) ->
	promise = new Parse.Promise()
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('patient', patientId)
	responseQuery.equalTo('status', 'started')
	responseQuery.include('questionnaire')
	responseQuery.first()
	.then (responseObj) ->
		if !_.isUndefined(responseObj)
			timeObj = getValidTimeFrame(responseObj.get('questionnaire'), responseObj.get('occurrenceDate'))
			if isValidMissedTime(timeObj)
				responseObj.set 'status', 'missed'
				responseObj.save()
				.then (responseObj) ->
					promise.resolve()
				, (error) ->
					promise.error error
			else
				promise.resolve()
		else
			timeObj = getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence'))
			if isValidMissedTime(timeObj)
				createResponse(scheduleObj.get('questionnaire').id, patientId, scheduleObj)#, 'missed', scheduleObj.get('nextOccurrence'))
				.then (responseObj) ->
					responseObj.set 'occurrenceDate', scheduleObj.get('nextOccurrence')
					responseObj.set 'status', 'missed'
					responseObj.save()
					.then (responseObj) ->
						scheduleQuery = new Parse.Query('Schedule')
						scheduleQuery.doesNotExist('patient')
						scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'))
						scheduleQuery.first()
						.then (scheduleQuestionnaireObj) ->
							newNextOccurrence = new Date (scheduleObj.get('nextOccurrence').getTime())
							newNextOccurrence.setTime(newNextOccurrence.getTime() + Number(scheduleQuestionnaireObj.get('frequency')) * 1000)
							scheduleObj.set 'nextOccurrence', newNextOccurrence
							scheduleObj.save()
							.then (scheduleObj) ->
								promise.resolve()
							, (error) ->
								promise.error error
						, (error) ->
							promise.error error
					, (error) ->
						promise.error error
				, (error) ->
					promise.error error
			else 
				promise.resolve()
	, (error) ->
		promise.error error
	promise




createResponse = (questionnaireId, patientId, scheduleObj) -> 
	promise = new Parse.Promise()
	questionnaireQuery = new Parse.Query("Questionnaire")
	questionnaireQuery.get(questionnaireId)
	.then (questionnaireObj) ->
		responseQuery = new Parse.Query('Response')
		responseQuery.equalTo('questionnaire', questionnaireObj)
		responseQuery.equalTo('patient', patientId)
		responseQuery.descending('occurrenceDate')
		responseQuery.notEqualTo('status', 'base_line')
		responseQuery.find()
		.then (responseObjs) ->
			responseObj = new Parse.Object "Response"
			responseObj.set 'patient', patientId
			responseObj.set 'hospital', questionnaireObj.get('hospital')
			responseObj.set 'project', questionnaireObj.get('project')
			responseObj.set 'questionnaire', questionnaireObj
			responseObj.set 'answeredQuestions', []
			responseObj.set 'schedule', scheduleObj
			responseObj.set 'sequenceNumber', responseObjs.length + 1
			responseObj.set 'baseLineFlagStatus', 'open'
			responseObj.set 'previousFlagStatus', 'open'
			#responseObj.set 'flagStatus', 'open'
			responseObj.save()
			.then (responseObj) ->
				promise.resolve responseObj
			, (error) ->
				promise.reject error
		, (error) ->
			promise.reject error			
	, (error) ->
		promise.reject error
	promise


isValidUpcomingTime = (timeObj) ->
	currentTime = new Date()

	if timeObj['lowerLimit'].getTime() > currentTime.getTime()
		true
	else
		false

isValidMissedTime = (timeObj) ->
	currentTime = new Date()

	if timeObj['upperLimit'].getTime() < currentTime.getTime()
		true
	else
		false

getValidTimeFrame = (questionnaireObj, occurrenceDate) ->
	gracePeriod = questionnaireObj.get('gracePeriod') * 1000

	upperLimit = new Date(occurrenceDate.getTime())
	upperLimit.setTime(occurrenceDate.getTime() + gracePeriod)

	lowerLimit = new Date(occurrenceDate.getTime())
	lowerLimit.setTime(lowerLimit.getTime() - gracePeriod)

	timeObj = {}

	timeObj['upperLimit'] = upperLimit
	timeObj['lowerLimit'] = lowerLimit

	timeObj

getValidPeriod = (scheduleObj) ->
	nextOccurrence = scheduleObj.get('nextOccurrence')
	gracePeriod = scheduleObj.get('questionnaire').get('gracePeriod') * 1000

	lowerLimit = new Date(nextOccurrence.getTime())
	lowerLimit.setTime(lowerLimit.getTime() - gracePeriod)

	upperLimit = new Date(nextOccurrence.getTime())
	upperLimit.setTime(upperLimit.getTime() + gracePeriod)

	timeObj = {}

	timeObj['lowerLimit'] = lowerLimit
	timeObj['upperLimit'] = upperLimit

	timeObj


isValidTime = (timeObj) ->
	currentTime = new Date()

	if (timeObj['lowerLimit'].getTime() <= currentTime.getTime()) and (timeObj['upperLimit'].getTime() >= currentTime.getTime())
		true
	else
		false







saveAnswer1 = (responseObj, questionObj, options, value) ->
	promiseArr = []
	promise = new Parse.Promise()
	responseObject = 
				"__type" : "Pointer",
				"className":"Response",
				"objectId":responseObj.id

	getAnswers = () ->
		if !_.isEmpty options
			_.each options, (optionId) ->
				optionQuery = new Parse.Query('Options')
				optionQuery.get(optionId)
				.then (optionObj) ->
					answer = new Parse.Object('Answer')
					answer.set "response",responseObject
					answer.set "patient", responseObj.get('patient')
					answer.set "question",questionObj
					answer.set "option",optionObj
					answer.set "value",value
					answerPromise = answer.save()
					promiseArr.push answerPromise
					console.log("saved")
				, (error) ->
					promise.reject error

		else

			answer = new Parse.Object('Answer')
			answer.set "response",responseObj
			answer.set "patient",responseObj.get('patient')
			answer.set "question",questionObj
			answer.set "value",value
			answerPromise = answer.save()
			console.log("saved")
			promiseArr.push answerPromise

		Parse.Promise.when(promiseArr)


	getCurrentAnswer(questionObj, responseObj)
	.then (hasAnswer) ->
		isEditable = questionObj.get('questionnaire').get('editable')
		
		if !isEditable and !_.isEmpty(hasAnswer)
			console.log "----------------------------------"
			console.log isEditable
			console.log hasAnswer
			console.log "----------------------------------"
			promise.resolve("already_answered")

		else if isEditable and !_.isEmpty(hasAnswer)
			console.log "=================================="
			console.log isEditable
			console.log hasAnswer

			answerQuery = new Parse.Query('Answer')
			answerQuery.equalTo('response', responseObj)
			answerQuery.equalTo('question', questionObj)
			answerQuery.find()
			.then (answers) ->
				promiseDelete = Parse.Promise.as()
				_.each answers, (answer) ->
					promiseDelete = promiseDelete
					.then ->
						answer.destroy()
					,(error) ->
						promise.error error
				promiseDelete
			, (error) ->
				promise.error error
			.then ->
				getAnswers().then -> 
					answeredQuestions = responseObj.get('answeredQuestions')
					if questionObj.id not in answeredQuestions
						answeredQuestions.push(questionObj.id)
					responseObj.set 'answeredQuestions', answeredQuestions
					responseObj.save()
					.then (responseObj) ->      
						promise.resolve(responseObj)    
					, (error) ->
						promise.error error         
				, (error) ->
					promise.error error
			, (error) ->
				promise.error error
		else
			console.log "++++++++++++++++++++++++++++++++++++++++++++++++++"
			console.log isEditable
			console.log hasAnswer

			getAnswers().then -> 
				answeredQuestions = responseObj.get('answeredQuestions')
				if questionObj.id not in answeredQuestions
					answeredQuestions.push(questionObj.id)
				responseObj.set 'answeredQuestions', answeredQuestions
				responseObj.save()
				.then (responseObj) ->      
					promise.resolve(responseObj)    
				, (error) ->
					promise.error error         
			, (error) ->
				promise.error error
	, (error) ->
		promise.error error

	promise





Parse.Cloud.define "saveAnswer", (request, response) ->
	responseId = request.params.responseId
	questionId = request.params.questionId
	options = request.params.options
	value = request.params.value

	responseQuery = new Parse.Query('Response')
	responseQuery.include('questionnaire')
	responseQuery.get(responseId)
	.then (responseObj) ->
		questionsQuery = new Parse.Query('Questions')
		questionsQuery.include('questionnaire')
		questionsQuery.get(questionId)
		.then (questionsObj) ->
			saveAnswer(responseObj, questionsObj, options, value)
			.then (answerObjs) ->
				#result = []
				#for answer in answerObjs
				#	result.push(answer.id)
				#response.success(result)
				response.success(answerObjs)
			, (error) ->
				response.error error
		, (error) ->
			response.error error
	, (error) ->
		response.error error


saveAnswer = (responseObj, questionsObj, options, value) ->
	promise = new Parse.Promise()

	if questionsObj.get('type') == 'single-choice'
		saveSingleChoice(responseObj, questionsObj, options)
		.then (answerObj) ->
			answeredQuestions = responseObj.get('answeredQuestions')
			if questionsObj.id not in answeredQuestions
				answeredQuestions.push(questionsObj.id)
			responseObj.set 'answeredQuestions', answeredQuestions
			responseObj.save()
			.then (responseObj) ->      
				promise.resolve()    
			, (error) ->
				promise.reject error
		, (error) ->
			promise.reject error
	else if questionsObj.get('type') == 'multi-choice'
		saveMultiChoice(responseObj, questionsObj, options)
		.then (answerObjs) ->
			answeredQuestions = responseObj.get('answeredQuestions')
			if questionsObj.id not in answeredQuestions
				answeredQuestions.push(questionsObj.id)
			responseObj.set 'answeredQuestions', answeredQuestions
			responseObj.save()
			.then (responseObj) ->      
				promise.resolve()    
			, (error) ->
				promise.reject error
		, (error) ->
			promise.reject error
	else if questionsObj.get('type') == 'input'
		saveInput(responseObj, questionsObj, options, value)
		.then (answerObj) ->
			answeredQuestions = responseObj.get('answeredQuestions')
			if questionsObj.id not in answeredQuestions
				answeredQuestions.push(questionsObj.id)
			responseObj.set 'answeredQuestions', answeredQuestions
			responseObj.save()
			.then (responseObj) ->      
				promise.resolve()    
			, (error) ->
				promise.reject error
		, (error) ->
			promise.reject error
	else
		saveDescriptive(responseObj, questionsObj, value)
		.then (answerObj) ->
			answeredQuestions = responseObj.get('answeredQuestions')
			if questionsObj.id not in answeredQuestions
				answeredQuestions.push(questionsObj.id)
			responseObj.set 'answeredQuestions', answeredQuestions
			responseObj.save()
			.then (responseObj) ->      
				promise.resolve()    
			, (error) ->
				promise.reject error
		, (error) ->
			promise.reject error
	promise



saveMultiChoice = (responseObj, questionsObj, options) ->
	promiseArr = []
	promise = new Parse.Promise()


	getAnswers = () ->
		promise1 = Parse.Promise.as();
		_.each options, (optionsId) ->
			promise1 = promise1
			.then () ->
				optionsQuery = new Parse.Query('Options')
				optionsQuery.get(optionsId)
				.then (optionsObj) ->
					answer = new Parse.Object('Answer')
					answer.set "response",responseObj
					answer.set "patient", responseObj.get('patient')
					answer.set "question",questionsObj
					answer.set "option",optionsObj
					#answer.set "flagStatus", "open"
					answer.set "score", optionsObj.get('score')
					answer.set 'project', responseObj.get('project')
					answer.save()
				, (error) ->
					promise.reject error
			, (error) ->
				promise.reject error
		promise1


	deleteAnswers = (answers) ->
		promise1 = Parse.Promise.as();
		_.each answers, (answerObj) ->
			promise1 = promise1
			.then () ->
				answerObj.destroy()
			, (error) ->
				promise.reject error
		promise1


	getCurrentAnswer(questionsObj, responseObj)
	.then (hasAnswer) ->
		isEditable = responseObj.get('questionnaire').get('editable')
		if !isEditable and !_.isEmpty(hasAnswer)
			promise.resolve("notEditable")
		else if isEditable and !_.isEmpty(hasAnswer)
			answerQuery = new Parse.Query('Answer')
			answerQuery.equalTo('question', questionsObj)
			answerQuery.equalTo('response', responseObj)
			answerQuery.find()
			.then (answers) ->
				deleteAnswers(answers)
				.then () ->
					getAnswers()
					.then () ->
						promise.resolve("saved")
					, (error) ->
						promise.reject error
				, (error) ->
					promise.reject error
			, (error) ->
				promise.reject error
		else
			getAnswers()
			.then () ->
				promise.resolve("saved")
			, (error) ->
				promise.reject("someError")
	, (error) ->
		promise.reject error
	promise


getFlag = (value) ->
	flag = ""
	if value <= -2
		flag = "red"
	else if value == -1
		flag = "amber"
	else if value == 0
		flag = "no_colour"
	else 
		flag = "green"
	flag


#Change the 'status' of the responseObj to 'completed'
Parse.Cloud.define "submitQuestionnaire", (request, response) ->
#	if !request.user
#		response.error('Must be logged in.')
#
#	else
	responseId = request.params.responseId
	responseQuery = new Parse.Query("Response")
	responseQuery.get(responseId)
	.then (responseObj) ->
		getBaseLineScores(responseObj) 
		.then (BaseLine) ->
			getPreviousScores(responseObj)
			.then (previous) ->
				responseObj.set "comparedToBaseLine", BaseLine['comparedToBaseLine']
				responseObj.set "comparedToPrevious", previous['comparedToPrevious']
				responseObj.set "baseLineFlag", BaseLine['baseLineFlag']
				responseObj.set "previousFlag", previous['previousFlag']
				responseObj.set "status", "completed"
				responseObj.set "totalScore", BaseLine['totalScore']
				responseObj.save()
				.then (responseObj) ->
					response.success "submitted_successfully"
				, (error) ->
					response.error error
			, (error) ->
				response.error error
		, (error) ->
			response.error error
	, (error) ->
		response.error error


getPreviousScores = (responseObj) ->
	promise = new Parse.Promise()
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('patient', responseObj.get('patient'))
	responseQuery.equalTo('questionnaire', responseObj.get('questionnaire'))
	responseQuery.equalTo('status', 'completed')
	responseQuery.descending('occurrenceDate')
	responseQuery.first()
	.then (previousResponseObj) ->
		if !_.isEmpty(previousResponseObj)
			answerQuery = new Parse.Query('Answer')
			answerQuery.include('question')
			answerQuery.equalTo('response', previousResponseObj)
			answerQuery.find()
			.then (previousAnswers) ->
				answerQuery = new Parse.Query('Answer')
				answerQuery.include('question')
				answerQuery.equalTo('response', responseObj)
				answerQuery.find()
				.then (answers) ->
					totalPreviousScore = 0
					totalAnswerScore = 0
					for answer in answers
						if answer.get('question').get('type') == 'single-choice'
							totalAnswerScore += answer.get('score')

					for answer in previousAnswers
						if answer.get('question').get('type') == 'single-choice'
							totalPreviousScore += answer.get('score')
					previous = {}
					previous['comparedToPrevious'] = totalAnswerScore - totalPreviousScore
					previous['previousFlag'] = getFlag(totalAnswerScore - totalPreviousScore)			
					promise.resolve(previous)
				, (error) ->
					promise.reject error
			, (error) ->
				promise.reject error
		else
			getBaseLineScores(responseObj)
			.then (baseLine) ->
				previous = {}
				previous['comparedToPrevious'] = baseLine['comparedToBaseLine']
				previous['previousFlag'] = baseLine['baseLineFlag']			
				promise.resolve(previous)
			, (error) ->
				promise.reject error
	, (error) ->
		promise.reject error
	promise




getBaseLineScores = (responseObj) -> 
	promise = new Parse.Promise()
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('patient', responseObj.get('patient'))
	responseQuery.equalTo('questionnaire', responseObj.get('questionnaire'))
	responseQuery.equalTo('status', 'base_line')
	responseQuery.first()
	.then (responseBaseLine) ->
		answerQuery = new Parse.Query('Answer')
		answerQuery.equalTo('response', responseBaseLine)
		answerQuery.include('question')
		answerQuery.find()
		.then (answersBaseLine) ->
			answerQuery = new Parse.Query('Answer')
			answerQuery.include('question')
			answerQuery.equalTo('response', responseObj)
			answerQuery.find()
			.then (answers) ->
				totalBaseLineScore = 0
				totalAnswerScore = 0
				for answer in answers
					if answer.get('question').get('type') == 'single-choice'
						totalAnswerScore += answer.get('score')

				for answer in answersBaseLine
					if answer.get('question').get('type') == 'single-choice'
						totalBaseLineScore += answer.get('score')
				BaseLine = {}
				BaseLine['totalScore'] = totalAnswerScore
				BaseLine['comparedToBaseLine'] = totalAnswerScore - totalBaseLineScore
				BaseLine['baseLineFlag'] = getFlag(totalAnswerScore - totalBaseLineScore)			
				promise.resolve(BaseLine)
			, (error) ->
				promise.reject error
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise	






getBaseLineValues = (responseObj, questionsObj, optionsObj) ->
	promise = new Parse.Promise()
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo('patient', responseObj.get('patient'))
	responseQuery.equalTo('questionnaire', responseObj.get('questionnaire'))
	responseQuery.equalTo('status', 'base_line')
	responseQuery.first()
	.then (responseBaseLine) ->
		answerQuery = new Parse.Query('Answer')
		answerQuery.equalTo('response', responseBaseLine)
		answerQuery.equalTo('question', questionsObj)
		answerQuery.first()
		.then (BaseLineAnswer) ->
			BaseLineValue = BaseLineAnswer.get('score')
			BaseLineValue = optionsObj.get('score') - BaseLineValue
			BaseLine  = {}
			BaseLine['comparedToBaseLine'] = BaseLineValue
			BaseLine['baseLineFlag'] = getFlag(BaseLineValue)
			promise.resolve(BaseLine)
		, (error) ->
			promise.reject error
	, (error) ->
		promise.reject error
	promise	



getPreviousQuestionnaireAnswer =  (questionObject, responseObj, patientId) ->
	promise = new Parse.Promise()

	answerQuery = new Parse.Query('Answer')
	answerQuery.equalTo("question", questionObject)
	answerQuery.include('response')
	answerQuery.equalTo("patient", patientId)
	answerQuery.notEqualTo("response", responseObj)
	answerQuery.descending('occurrenceDate');
	answerQuery.find()
	.then (answerObjects) ->
		result = {}
		answerObjects = (answerObj for answerObj in answerObjects when answerObj.get('response').get('status') == 'completed')
		if !_.isEmpty answerObjects

				optionIds = []
				if questionObject.get('type') == 'multi-choice'
					first = answerObjects[0].get('response').id
					optionIds = (answerObj.get('option').id  for answerObj in answerObjects when answerObj.get('response').id == first)
				else 
					if !_.isUndefined(answerObjects[0])
						optionIds = [answerObjects[0].get('option').id] if !_.isUndefined(answerObjects[0].get('option')) 

				result = 
					"optionId" : optionIds
					"value" : answerObjects[0].get('value')
					"date" : answerObjects[0].updatedAt
					"answerId": answerObjects[0].id

		promise.resolve result
	, (error) ->
		promise.reject error

	promise


getPreviousValues = (responseObj, questionsObj, optionsObj) ->
	promise = new Parse.Promise()
	getPreviousQuestionnaireAnswer(questionsObj, responseObj, responseObj.get('patient'))
	.then (previousQuestion) ->
		console.log "========================================================="
		console.log previousQuestion
		console.log "---------------------------------------------------------"
	
		if !_.isEmpty(previousQuestion)
			answerQuery = new Parse.Query('Answer')
			answerQuery.get(previousQuestion['answerId'])
			.then (answerObj) ->
				previousValue = answerObj.get('score')
				previousValue = optionsObj.get('score') - previousValue
				previousFlag = ""
				if previousValue <= -2
					previousFlag = "red"
				else if previousValue == -1
					previousFlag = "amber"
				else if previousValue == 0
					previousFlag = "no_colour"
				else 
					previousFlag = "green"

				previous  = {}
				previous['comparedToPrevious'] = previousValue
				previous['previousFlag'] = previousFlag
				promise.resolve(previous)
			, (error) ->
				promise.reject error
		else
			getBaseLineValues(responseObj, questionsObj, optionsObj)
				.then (BaseLine) ->
					previous  = {}
					previous['comparedToPrevious'] = BaseLine['comparedToBaseLine']
					previous['previousFlag'] = BaseLine['baseLineFlag']
					promise.resolve(previous)
				, (error) ->
					promise.reject error
	, (error) ->
		promise.reject error
	promise

saveSingleChoice = (responseObj, questionsObj, options) ->
	promise = new Parse.Promise()
	getCurrentAnswer(questionsObj, responseObj)
	.then (hasAnswer) ->
		isEditable = responseObj.get('questionnaire').get('editable')
		if !isEditable and !_.isEmpty(hasAnswer)
			promise.resolve("notEditable")
		else if isEditable and !_.isEmpty(hasAnswer)
			answerQuery = new Parse.Query('Answer')
			answerQuery.equalTo('response', responseObj)
			answerQuery.equalTo('question', questionsObj)
			answerQuery.first()
			.then (answer) ->
				if !_.isEmpty(options)
					optionsQuery = new Parse.Query "Options"
					optionsQuery.equalTo('question', questionsObj)
					optionsQuery.equalTo('objectId', options[0])
					optionsQuery.first()
					.then (optionsObj) ->
						getBaseLineValues(responseObj, questionsObj, optionsObj)
						.then (BaseLine) ->
							getPreviousValues(responseObj, questionsObj, optionsObj)
							.then (previous) ->
								answer.set "option", optionsObj
								answer.set "score", optionsObj.get('score')
								answer.set "comparedToBaseLine", BaseLine['comparedToBaseLine']
								answer.set "comparedToPrevious", previous['comparedToPrevious']
								answer.set "baseLineFlag", BaseLine['baseLineFlag']
								answer.set "previousFlag", previous['previousFlag']
								answer.save()
								.then (answer) ->
									promise.resolve(answer)
								, (error) ->
									promise.reject error
							, (error) ->
								promise.reject error
						, (error) ->
							promise.reject error
					, (error) ->
						promise.reject error
				else
					promise.reject "noOptionSelected"
			, (error) ->
				promise.reject error
		else
			answer = new Parse.Object('Answer')
			answer.set "response",responseObj
			answer.set "patient",responseObj.get('patient')
			answer.set "question",questionsObj
			#answer.set "flagStatus", "open"
			answer.set 'project', responseObj.get('project')
			answer.save()
			.then (answer) ->
				if !_.isEmpty(options)
					optionsQuery = new Parse.Query "Options"
					optionsQuery.equalTo('question', questionsObj)
					optionsQuery.equalTo('objectId', options[0])
					optionsQuery.first()
					.then (optionsObj) ->
						answer.set "option", optionsObj
						answer.set "score", optionsObj.get('score')
						answer.save()
						.then (answer) ->
							getBaseLineValues(responseObj, questionsObj, optionsObj)
							.then (BaseLine) ->
								getPreviousValues(responseObj, questionsObj, optionsObj)
								.then (previous) ->
									answer.set "option", optionsObj
									answer.set "score", optionsObj.get('score')
									answer.set "comparedToBaseLine", BaseLine['comparedToBaseLine']
									answer.set "comparedToPrevious", previous['comparedToPrevious']
									answer.set "baseLineFlag", BaseLine['baseLineFlag']
									answer.set "previousFlag", previous['previousFlag']
									answer.set "baseLineFlagStatus", 'open'
									answer.set "previousFlagStatus", 'open'
									answer.save()
									.then (answer) ->
										promise.resolve(answer)
									, (error) ->
										promise.reject error
								, (error) ->
									promise.reject error
							, (error) ->
								promise.reject error
						, (error) ->
							promise.reject error
					, (error) ->
						promise.reject error
				else
					promise.reject("noOptionSelected")
			, (error) ->
				promise.reject error
	, (error) ->
		promise.reject error
	promise	


saveInput = (responseObj, questionsObj, options, value) ->
	promise = new Parse.Promise()
	getCurrentAnswer(questionsObj, responseObj)
	.then (hasAnswer) ->
		isEditable = responseObj.get('questionnaire').get('editable')
		if !isEditable and !_.isEmpty(hasAnswer)
			promise.resolve("notEditable")
		else if isEditable and !_.isEmpty(hasAnswer)
			answerQuery = new Parse.Query('Answer')
			answerQuery.equalTo('response', responseObj)
			answerQuery.equalTo('question', questionsObj)
			answerQuery.first()
			.then (answer) ->
				if !_.isEmpty(options)
					optionsQuery = new Parse.Query "Options"
					optionsQuery.equalTo('question', questionsObj)
					optionsQuery.equalTo('objectId', options[0])
					optionsQuery.first()
					.then (optionsObj) ->
						answer.set "option", optionsObj
						answer.set "score", optionsObj.get('score')
						answer.set "value", value
						answer.save()
						.then (answer) ->
							promise.resolve(answer)
						, (error) ->
							promise.reject error
					, (error) ->
						promise.reject error
				else
					answer.set "value", value
					answer.save()
					.then (answer) ->
						promise.resolve(answer)
					, (error) ->
						promise.reject error
			, (error) ->
				promise.reject error
		else
			answer = new Parse.Object('Answer')
			answer.set "response",responseObj
			answer.set "patient",responseObj.get('patient')
			answer.set "question",questionsObj
			answer.set "value",value
			#answer.set "flagStatus", "open"
			answer.set 'project', responseObj.get('project')
			answer.save()
			.then (answer) ->
				if !_.isEmpty(options)
					optionsQuery = new Parse.Query "Options"
					optionsQuery.equalTo('question', questionsObj)
					optionsQuery.equalTo('objectId', options[0])
					optionsQuery.first()
					.then (optionsObj) ->
						answer.set "option", optionsObj
						answer.set "score", optionsObj.get('score')
						answer.save()
						.then (answer) ->
							promise.resolve(answer)
						, (error) ->
							promise.reject error
					, (error) ->
						promise.reject error
				else
					promise.resolve(answer)
			, (error) ->
				promise.reject error
	, (error) ->
		promise.reject error

	promise




saveDescriptive = (responseObj, questionsObj, value) ->
	promise = new Parse.Promise()

	getCurrentAnswer(questionsObj, responseObj)
	.then (hasAnswer) ->
		isEditable = responseObj.get('questionnaire').get('editable')
		if !isEditable and !_.isEmpty(hasAnswer)
			promise.resolve("notEditable")
		else if isEditable and !_.isEmpty(hasAnswer)
			answerQuery = new Parse.Query('Answer')
			answerQuery.equalTo('response', responseObj)
			answerQuery.equalTo('question', questionsObj)
			answerQuery.first()
			.then (answer) ->
				answer.set "value", value
				answer.save()
				.then (answer) ->
					promise.resolve(answer)
				, (error) ->
					promise.reject error
		else
			answer = new Parse.Object('Answer')
			answer.set "response",responseObj
			answer.set "patient",responseObj.get('patient')
			answer.set "question",questionsObj
			answer.set "value",value
			answer.set 'project', responseObj.get('project')
			#answer.set "flagStatus", "closed"
			answer.save()
			.then (answer) ->
				promise.resolve(answer)
			, (error) ->
				promise.reject error
	, (error) ->
		promise.reject error

	promise


Parse.Cloud.define "baseLine", (request, response) ->
	responseId = request.params.responseId
	questionId = request.params.questionId
	options = request.params.options
	value = request.params.value
	console.log "============================"
	console.log (new Date())
	console.log "==========================="
	responseQuery = new Parse.Query('Response')
	responseQuery.include('questionnaire')
	responseQuery.get(responseId)
	.then (responseObj) ->
		questionsQuery = new Parse.Query('Questions')
		questionsQuery.include('questionnaire')
		questionsQuery.get(questionId)
		.then (questionsObj) ->
			answer = new Parse.Object('Answer')
			answer.set "question", questionsObj
			answer.set "patient", responseObj.get('patient')
			answer.set "response", responseObj
			answer.set "score", value
			answer.set 'project', responseObj.get('project')
			answer.save()
			.then (answerObj) ->
				getNextQuestion questionsObj, []
				.then (question) ->

				#result = []
				#for answer in answerObjs
				#	result.push(answer.id)
				#response.success(result)
					response.success(question)
			, (error) ->
				response.error error
		, (error) ->
			response.error error
	, (error) ->
		response.error error
