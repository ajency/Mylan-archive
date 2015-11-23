
Parse.Cloud.define 'getQuestionnaire', (request, response) ->
    projectId = request.params.projectId
 
    projectObj = new Parse.Query('Project')
    projectObj.equalTo("objectId", projectId)
    projectObj.first()
    .then (projectobject) ->
        if _.isEmpty userobject
            result = 
                "message" : 'project does not exits' 
                "code" : 'invalid_project'   
                "status" : '404'
            response.success result 
        else
            questionnaireQuery = new Parse.Query('Questionnaire')
            questionnaireQuery.equalTo("project", projectobject)
            questionnaireQuery.find()
            .then (questionnaireObject) ->
                questions = {}
                getQuestions(questionnaireObject)
                .then (questionData) ->
                    questions = questionData
                , (error) ->
                    response.error error

                response.success result
            , (error) ->
                response.error error

    , (error) ->
        response.error error


getQuestion =  ( questionnaireObject ) ->
    promise = new Parse.Promise()

    questionQuery = new Parse.Query('Questionnaire')
    questionQuery.equalTo("questionnaire", questionnaireObject)
    questionQuery.first()
    .then (questionObject) ->
        options ={}
        getoptions(questionObject)
        .then (optionsData) ->
            options = optionsData
        , (error) ->
            response.error error

        result = 
            "id" : questionObject.id
            "name" : questionObject.get('question')  
            "type" : questionObject.get('type')
            "options" : options  

        promise.resolve result
    , (error) ->
        promise.resolve error

    promise


getoptions =  ( questionnaireObject ) ->
    promise = new Parse.Promise()

    optionsQuery = new Parse.Query('Options')
    optionsQuery.equalTo("question", questionnaireObject)
    optionsQuery.find()
    .then (optionObject) ->
        promise.resolve optionObject
    , (error) ->
        promise.resolve error

    promise


    
    


