Parse.Cloud.define "startQuestionnaire", (request, response) ->
	responseId = request.params.responseId
	questionnaireId = request.params.questionnaireId
	patientId = request.params.patientId

	if (responseId != "") and (!_.isUndefined questionnaireId) and (!_.isUndefined patientId)
		responseQuery = new Parse.Query("Response")
		responseQuery.get(responseId)
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

	else if  (responseId == "") and (!_.isUndefined questionnaireId) and (!_.isUndefined patientId)
		createResponse questionnaireId, patientId
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
				if _.isUndefined questionObj.get 'previousQuestion'
					if not questionObj.get 'isChild'
						true
					else
						false
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
	
	if questionObj.get('type') == 'single-choice'
		answerQuery.first()
		.then (answerObj) ->
			if !_.isUndefined(answerObj)
				options.push(answerObj.get('option').get('label'))
				hasAnswer['value'] =  answerObj.get('value')
				hasAnswer['option'] = options
	 		
			promise.resolve(hasAnswer)
		, (error) ->
			promise.reject error



	else if questionObj.get('type') == 'multi-choice'
		answerQuery.find()
		.then (answerObjs) ->
			options.push answerObj.get('option').get('label') for answerObj in answerObjs
			hasAnswer['option'] = options
			if (!_.isUndefined(answerObjs[0]))
				hasAnswer['value'] =  answerObjs[0].get('value')
			else
				hasAnswer['value'] =  ""
			promise.resolve(hasAnswer)

		, (error) ->
			promise.reject error

	else 
		answerQuery.first()
		.then (answerObj) ->
			if !_.isUndefined(answerObj)
				hasAnswer['option'] = []
				hasAnswer['value'] =  answerObj.get('value')
			promise.resolve(hasAnswer)
		, (error) ->
			promise.reject error

	promise



getQuestionData = (questionObj, responseObj, patientId) ->
	promise = new Parse.Promise()
	questionData = {}
	questionData['responseId'] = responseObj.id
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


Parse.Cloud.define 'saveAnswer', (request, response) ->
    responseId = request.params.responseId
    questionId = request.params.questionId
    options = request.params.options
    value = request.params.value
    
    responseQuery = new Parse.Query('Response')
    responseQuery.get(responseId)
    .then (responseObj) ->
        questionQuery = new Parse.Query('Questions')
        questionQuery.include('nextQuestion')
        questionQuery.include('previousQuestion')

        questionQuery.get(questionId)

        .then (questionObj) ->
            saveAnswer responseObj, questionObj, options, value
            .then (answersArray) ->
            	getNextQuestion(questionObj, options)
            	.then (nextQuestionObj) ->
	                getQuestionData nextQuestionObj, responseObj, responseObj.get('patient')
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


getNextQuestion = (questionObj, option) ->
	promise = new Parse.Promise()

	if questionObj.get('type') == 'single-choice' and (!_.isUndefined(questionObj.get('condition')))
		optionsQuery = new Parse.Query "Options"
		optionsQuery.get(option[0])
		.then (optionObj) ->

			conditions = questionObj.get('condition')
			conditionalQuestion = ( condition['questionId'] for condition in conditions when condition['optionId'] == optionObj.id)
			questionQuery = new Parse.Query("Questions")
			questionQuery.get(conditionalQuestion[0])
			.then (optionQuestionObj) ->
				promise.resolve(optionQuestionObj)

			,(error) ->
        		promise.error error
        
		,(error) ->
        	promise.error error

        		

	else
		promise.resolve(questionObj.get('nextQuestion'))

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
                optionIds = (answerObj.get('option').id  for answerObj in answerObjects)
            else if questionObject.get('type') == 'single-choice'
                optionIds = [answerObjects[0].get('option').id]

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


    if !_.isEmpty options
        _.each options, (optionId) ->
            optionQuery = new Parse.Query('Options')
            optionQuery.get(optionId)
            .then (optionObj) ->
                answer = new Parse.Object('Answer')
                answer.set "response",responseObj
                answer.set "patient", responseObj.get('patient')
                answer.set "question",questionObj
                answer.set "option",optionObj
                answer.set "value",value
                answerPromise = answer.save()
                promiseArr.push answerPromise
            
            , (error) ->
                promise.reject error

    else
        
        answer = new Parse.Object('Answer')
        answer.set "response",responseObj
        answer.set "patient",responseObj.get('patient')
        answer.set "question",questionObj
        answer.set "value",value
        answerPromise = answer.save()
        promiseArr.push answerPromise

    Parse.Promise.when(promiseArr).then -> 
    	#answeredQuestions = responseObj.get('answeredQuestions')
    	#answeredQuestions[questionObj.id] = questionObj.id
    	#responseObj.set 'answeredQuestions', answeredQuestions
    	#responseObj.save()
    	#.then (responseObj) ->       
        promise.resolve(responseObj)    
    	#, (error) ->
        #	promise.error error         
    , (error) ->
        promise.error error




Parse.Cloud.define "previousQuestion", (request, response) ->
    responseId = request.params.responseId
    questionId = request.params.questionId
    options = request.params.options
    value = request.params.value
    
    responseQuery = new Parse.Query('Response')
    responseQuery.get(responseId)
    .then (responseObj) ->
        questionQuery = new Parse.Query('Questions')
        questionQuery.include('previousQuestion')
        questionQuery.get(questionId)

        .then (questionObj) ->
            saveAnswer responseObj, questionObj, options, value
            .then (answersArray) ->
            	getNextQuestion(questionObj, options)
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


