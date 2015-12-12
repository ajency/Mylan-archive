Parse.Cloud.define "startQuestionnaire", (request, response) ->
	responseId = request.params.responseId
	questionnaireId = request.params.questionnaireId
	patientId = request.params.patientId

	if (responseId != "") and (!_.isUndefined responseId) and (!_.isUndefined questionnaireId) and (!_.isUndefined patientId)
		
		responseQuery = new Parse.Query("Response")
		responseQuery.get(responseId)
		.then (responseObj) ->
			if responseObj.get('status') == 'Completed'
				response.error "Questionnaire already answered"

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
		createResponse questionnaireId, patientId
		.then (responseObj) ->
			responseObj.set 'status', 'Started'
			responseObj.save()
			.then (responseObj) ->
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
	else
		response.error "Invalid request."




firstQuestion = (questionnaireId) ->
	promise = new Parse.Promise()
	questionnaireQuery = new Parse.Query("Questionnaire")
	questionnaireQuery.get(questionnaireId)
	.then (questionnaireObj) ->
		questionsQuery = new Parse.Query("Questions")
		questionsQuery.equalTo('questionnaire', questionnaireObj)
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


createResponse = (questionnaireId, patientId) ->
	promise = new Parse.Promise()
	questionnaireQuery = new Parse.Query("Questionnaire")
	questionnaireQuery.get(questionnaireId)
	.then (questionnaireObj) ->
		responseObj = new Parse.Object "Response"
		responseObj.set 'patient', patientId
		responseObj.set 'hospital', questionnaireObj.get('hospital')
		responseObj.set 'project', questionnaireObj.get('project')
		responseObj.set 'questionnaire', questionnaireObj
		responseObj.set 'answeredQuestions', []
		responseObj.save()
		.then (responseObj) ->
			promise.resolve responseObj
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
			hasAnswer['option'] = options
			if (!_.isUndefined(answerObjs[0]))
				hasAnswer['value'] =  answerObjs[0].get('value')
				hasAnswer['date'] = answerObjs[0].get('updatedAt')
			else
				hasAnswer['value'] =  ""

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

	promise	


Parse.Cloud.define 'getNextQuestion', (request, response) ->
	responseId = request.params.responseId
	questionId = request.params.questionId
	options = request.params.options
	value = request.params.value

	responseQuery = new Parse.Query('Response')
	responseQuery.get(responseId)
	.then (responseObj) ->
		if responseObj.get('status') == 'Completed'
			response.error "questionnaire already submitted."
		else 
			questionQuery = new Parse.Query('Questions')
			questionQuery.include('nextQuestion')
			questionQuery.include('previousQuestion')

			questionQuery.get(questionId)

			.then (questionObj) ->
				saveAnswer responseObj, questionObj, options, value
				.then (answersArray) ->

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
		console.log "getQuestionData"
		if !_.isUndefined questionObj.get('nextQuestion')
			promise.resolve(questionObj.get('nextQuestion'))

		else if _.isUndefined questionObj.get('nextQuestion') and !responObj.get(isChild)
			promise.resolve({})

		else
			while responObj.get(isChild) or !_.isUndefined(responseObj.get(previousQuestion))
				reponseObj = reponseObj.get(previousQuestion)
			if !_.isUndefined questionObj.get('nextQuestion')
				promise.resolve(questionObj.get('nextQuestion'))
			else
				promise.resolve({})




	if questionObj.get('type') == 'single-choice' and (!_.isUndefined(questionObj.get('condition')))
		optionsQuery = new Parse.Query "Options"
		optionsQuery.get(option[0])
		.then (optionObj) ->

			conditions = questionObj.get('condition')
			conditionalQuestion = ( condition['questionId'] for condition in conditions when condition['optionId'] == optionObj.id)
			if conditionalQuestion.length != 0
				questionQuery = new Parse.Query("Questions")
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


getPreviousQuestionnaireAnswer =  (questionObject, responseObj, patientId) ->
	promise = new Parse.Promise()

	answerQuery = new Parse.Query('Answer')
	answerQuery.equalTo("question", questionObject)
	answerQuery.equalTo("patient", patientId)
	answerQuery.notEqualTo("response", responseObj)
	answerQuery.descending('updatedAt');
	answerQuery.find()
	.then (answerObjects) ->
		result = {}

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

		promise.resolve result
	, (error) ->
		promise.reject error

	promise



saveAnswer = (responseObj, questionObj, options, value) ->
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




Parse.Cloud.define "getPreviousQuestion", (request, response) ->
	responseId = request.params.responseId
	questionId = request.params.questionId
	options = request.params.options
	value = request.params.value

	responseQuery = new Parse.Query('Response')
	responseQuery.get(responseId)
	.then (responseObj) ->
		if responseObj.get('status') == 'Completed'
			response.error "Questionnaire already answered."
		else
			questionQuery = new Parse.Query('Questions')
			questionQuery.include('previousQuestion')
			questionQuery.get(questionId)

				.then (questionObj) ->
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
				,(error) ->
					response.error error
	,(error) ->
		response.error error


Parse.Cloud.define 'getSummary', (request, response) ->
	responseId = request.params.responseId
	responseQuery = new Parse.Query('Response')
	responseQuery.equalTo("objectId", responseId)
	responseQuery.first()
	.then (responseObj) ->
		getSummary responseObj
		.then (answerObjects) ->
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


Parse.Cloud.define "submitQuestionnaire", (request, response) ->
	responseId = request.params.responseId
	responseQuery = new Parse.Query("Response")
	responseQuery.get(responseId)
	.then (responseObj) -> 
		responseObj.set "status", "Completed"
		responseObj.save()
		.then (responseObj) ->
			response.success responseObj.id
		, (error) ->
			response.error error
	, (error) ->
		response.error error