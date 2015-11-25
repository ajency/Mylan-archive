
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
                questions = {}
                getQuestion(questionnaireObject,[])
                .then (questionData) ->
                    result = 
                        "id" : questionnaireObject.id
                        "name" : questionnaireObject.get('name')  
                        "description" : questionnaireObject.get('description')
                        "question" : questionData
                    response.success result
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
        getQuestion(questionnaireObject,questionIds)
        .then (questionData) ->
            response.success questionData
        , (error) ->
            response.error error

    , (error) ->
        response.error error

Parse.Cloud.define 'getQuestion', (request, response) ->
    responseId = request.params.responseId
    questionIds = request.params.questionIds
 
    questionQuery = new Parse.Query('Question')
    questionQuery.equalTo("objectId", questionIds)
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
     

        
    






    
    


