
Parse.Cloud.define 'getQuestionnaire', (request, response) ->
    projectId = request.params.projectId
 
    projectObj = new Parse.Query('Project')
    projectObj.equalTo("objectId", projectId)
    projectObj.first()
    .then (projectobject) ->
        if _.isEmpty projectobject
            result = 
                "message" : 'project does not exits' 
                "code" : 'invalid_project'   
                "status" : '404'
            response.success result 
        else
            questionnaireQuery = new Parse.Query('Questionnaire')
            questionnaireQuery.equalTo("project", projectobject)
            questionnaireQuery.first()
            .then (questionnaireObject) ->
                addResponse(projectobject, request.params.hospitalId, request.params.patientId, questionnaireObject)
                .then (responseObj) ->
                    questions = {}
                    getQuestion(questionnaireObject,[])
                    .then (questionData) ->
                        result = 
                            "id" : questionnaireObject.id
                            "name" : questionnaireObject.get('name')  
                            "description" : questionnaireObject.get('description')
                            "question" : questionData
                            "response" : responseObj.id


                        response.success result
                    , (error) ->
                        response.error error


                , (error) ->
                    response.error error

            , (error) ->
                response.error error

    , (error) ->
        response.error error

Parse.Cloud.define 'getNextQuestion', (request, response) ->
    questionnaireId = request.params.questionnaireId
    questionIds = request.params.questionIds
 
    questionnaireQuery = new Parse.Query('Questionnaire')
    questionnaireQuery.equalTo("objectId", questionnaireId)
    questionnaireQuery.first()
    .then (questionnaireObject) ->
        questions = {}
        getQuestion(questionnaireObject,questionIds, )
        .then (questionData) ->
            response.success questionData
        , (error) ->
            response.error error

    , (error) ->
        response.error error

Parse.Cloud.define 'getQuestion', (request, response) ->
    responseId = request.params.responseId
    questionId = request.params.questionId
    answer = request.params.answer
 
    questionQuery = new Parse.Query('Questions')
    questionQuery.equalTo("objectId", questionId)
    questionQuery.first()
    .then (questionObject) ->
        result = {}

        if !_.isEmpty questionObject
            options ={}
            getoptions(questionObject)
            .then (optionsData) ->
                options = optionsData
                getAnswer(answer, responseId)
                .then (answerObj) -> 
                    result = 
                        "id" : questionObject.id
                        "question" : questionObject.get('question')  
                        "type" : questionObject.get('type')
                        "options" : options
                        "answer": answerObj
                    console.log result
                    response.success result

                ,(error) ->
                    response.error error

                
            , (error) ->
                response.error error
    , (error) ->
        response.error error

getQuestion =  ( questionnaireObject ,questionIds) ->
    promise = new Parse.Promise()

    questionQuery = new Parse.Query('Questions')
    questionQuery.equalTo("questionnaire", questionnaireObject)
    questionQuery.notContainedIn("objectId", questionIds)
    questionQuery.first()
    .then (questionObject) ->
        result = {}

        if !_.isEmpty questionObject
            options ={}
            getoptions(questionObject)
            .then (optionsData) ->
                console.log "optionsData"
                options = optionsData

                result = 
                    "id" : questionObject.id
                    "question" : questionObject.get('question')  
                    "type" : questionObject.get('type')
                    "options" : options

                promise.resolve result

            , (error) ->
                console.log "getQuestion option ERROR"
                response.error error

    , (error) ->
        promise.reject error

    promise

addResponse = (projectObj, hospitalId, patientId, questionnaireObj) ->
    promise = new Parse.Promise()
    hospitalObj = {}
    
    hospitalQuery = new Parse.Query('Hospital')
    hospitalQuery.get(hospitalId)
    .then (hospitalObj) ->  
        Response = Parse.Object.extend 'Response'
        responseObj = new Response()
        responseObj.set('patient', patientId)
        responseObj.set('project', projectObj)
        responseObj.set('hospital',hospitalObj)
        responseObj.set('questionnaire', questionnaireObj)
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

getAnswer = (answer, responseId) ->
    responseQuery = new Parse.Query('Response')
    promise = new Parse.Promise()
    if answer
        responseQuery.get(responseId)
        .then (responseObj) ->
            answerQuery = new Parse.Query('Answer')
            answerQuery.equalTo("response", responseObj)
            answerQuery.find()
            .then (answerObj) ->
                promise.resolve answerObj
            , (error) ->
                promise.reject error
        , (error) ->
            promise.reject error
    else
        promise.resolve {}
    promise

getoptions =  ( questionObject ) ->
    promise = new Parse.Promise()
    optionsQuery = new Parse.Query('Options')
    optionsQuery.equalTo("question", questionObject)
    optionsQuery.find()
    .then (optionObjects) ->
        result ={}
        options = _.map(optionObjects, (optionObject) ->
                     result = 
                        "id" : optionObject.id
                        "label" : optionObject.get('label')
                        "score" : optionObject.get('score')
                        "subQuestion": optionObject.get('subQuestion')  
                    )
        
        promise.resolve options
    , (error) ->
        promise.reject error

    promise


Parse.Cloud.define 'saveAnswer', (request, response) ->
    responseId = request.params.responseId
    patientId = parseInt request.params.patientId
    questionId = request.params.questionId
    options = request.params.options
    value = request.params.value
    promiseArr = []

    if !_.isEmpty options
       
        
        _.each options , (optionId) ->
            Options = Parse.Object.extend("Options") 
            option = new Options()
            option.id = optionId

            Response = Parse.Object.extend("Response") 
            responseObj = new Response()
            responseObj.id = responseId

            Questions = Parse.Object.extend("Questions") 
            question = new Questions()
            question.id = questionId

            AnswerData = 
                response: responseObj
                patient: patientId
                question : question
                option : option
                value : value
            
 
            Answer = Parse.Object.extend("Answer") 
            answer = new Answer()
            answer.set "response",responseObj
            answer.set "patient",patientId
            answer.set "question",question
            answer.set "option",option
            answer.set "value",value
            answerPromise = answer.save()
            promiseArr.push answerPromise
    else
        Response = Parse.Object.extend("Response") 
        responseObj = new Response()
        responseObj.id = responseId

        Questions = Parse.Object.extend("Questions") 
        question = new Questions()
        question.id = questionId

        AnswerData = 
            response: responseObj
            patient: patientId
            question : question
            value : value
        

        Answer = Parse.Object.extend("Answer") 
        answer = new Answer()
        answer.set "response",responseObj
        answer.set "patient",patientId
        answer.set "question",question
        answer.set "value",value
        answerPromise = answer.save()
        promiseArr.push answerPromise



    Parse.Promise.when(promiseArr).then ->          
        response.success "Saved"
    , (error) ->
        response.error error
     

        
    
Parse.Cloud.define "addSubQuestion", (request, response) ->
    optionQuery = new Parse.Query('Options')
    optionQuery.get request.params.optionId
    .then (optionObj) ->
        subQuestionQuery = new Parse.Query('Questions')
        subQuestionQuery.get request.params.subQuestionId
        .then (subQuestion) ->
            optionObj.set "subQuestion", subQuestion
            optionObj.save()
            .then (optionObj) ->
                response.success optionObj
            , (error) ->
                response.error error
        , (error) ->
            response.error error
     , (error) ->
        response.error error





    
    


