_ = require('underscore.js')
moment = require('cloud/moment.js')


Parse.Cloud.define 'doSetup', (request, response) ->
    referenceCode = request.params.referenceCode
    deviceIdentifier = request.params.deviceIdentifier
    # verify user by given refercecode
    userObj = new Parse.Query(Parse.User)
    userObj.equalTo("referenceCode", referenceCode)
    userObj.first()
    .then (userobject) ->
        if _.isEmpty userobject
            result = 
                "message" : 'reference code does not match' 
                "code" : 'invalid_reference_code'   
                "status" : '404'
            response.success result 
        else
            userId = userobject.id
            hospitalData = {}
            getHospitalData(userobject)
            .then (userHospitalData) ->
                hospitalData = userHospitalData
            , (error) ->
                response.error error

            userDeviceQuery = new Parse.Query('UserDevices')
            userDeviceQuery.equalTo("user", userobject)
            userDeviceQuery.find()
            .then (userDeviceObjects) ->
                userDeviceCount = userDeviceObjects.length
                deviceExist = {}
                if userDeviceCount == 0
                    result = 
                        "userId" : userobject.id
                        "hospitalData" : hospitalData
                        "message" : 'do new setup'    
                        "code" : 'new_setup'   
                        "status" : '500'
                else
                    deviceExist = _.find(userDeviceObjects, (userDeviceObject) ->
                                    console.log userDeviceObject.get('deviceIdentifier')
                                    if (userDeviceObject.get('deviceIdentifier') == deviceIdentifier) 
                                        console.log userDeviceObject
                                        return userDeviceObject
                                    )

                    if  !_.isEmpty deviceExist
                        result = 
                            "userId" : userobject.id
                            "hospitalData" : hospitalData
                            "message" : 'Device exist'    
                            "code" : 'do_login'   
                            "status" : '200'
                    else
                        result = 
                            "userId" : userobject.id
                            "hospitalData" : hospitalData
                            "message" : 'Device does not exist'    
                            "code" : 'new_setup'   
                            "status" : '404'

                response.success result
            , (error) ->
                response.error error

    , (error) ->
        response.error error


getHospitalData =  ( userobject ) ->
    promise = new Parse.Promise()

    hospitalUserQuery = new Parse.Query('HospitalUser')
    hospitalUserQuery.equalTo("user", userobject)
    hospitalUserQuery.include("hospital")
    hospitalUserQuery.include("hospital.group")
    hospitalUserQuery.first()
    .then (hospitalUserObjects) ->
        result = 
            "id" : hospitalUserObjects.id
            "name" : hospitalUserObjects.get('hospital').get('name')   
            "group" : hospitalUserObjects.get('hospital').get('group').get('name')   

        promise.resolve result
    , (error) ->
        promise.resolve error

    promise

storeDeviceData =  (request, response) ->
    promise = new Parse.Promise()

    deviceType = request.params.deviceType
    deviceIdentifier = request.params.deviceIdentifier
    deviceOS = request.params.deviceOS
    accessType = request.params.accessType

    UserDevice = Parse.Object.extend('UserDevices')
    userDevice = new UserDevice()

    userDevice.set "deviceType", deviceType
    userDevice.set "deviceIdentifier" , deviceIdentifier
    userDevice.set "deviceOS" , deviceOS
    userDevice.set "accessType" , accessType
  
    userDevice.save() 
    .then (userDeviceObj) ->
        result = 
            "id" : userDeviceObj.id
        promise.resolve result
    , (error) ->
        promise.resolve error

    promise

Parse.Cloud.define 'resetPassword', (request, response) ->
    userId = request.params.userId
    newpassword = request.params.newpassword
    # verify user by given refercecode
    userObj = new Parse.Query(Parse.User)
    userObj.equalTo("objectId", userId)
    userObj.first()
    .then (userobject) ->
        if _.isEmpty userobject
            result = 
                "message" : 'User does not exist' 
                "status" : '404'
            response.success result 
        else
            userobject.set "password", newpassword
            userobject.save() 
            .then (userDeviceObj) ->
                result = 
                    "message" : 'User password successfuly updated ' 
                    "status" : '201'
                promise.resolve result
            , (error) ->
                promise.resolve error

    , (error) ->
        response.error error


Parse.Cloud.define 'userLogin', (request, response) ->
    referenceCode = request.params.referenceCode
    password = request.params.password
    # verify user by given refercecode
    userObj = new Parse.Query(Parse.User)
    userObj.equalTo("objectId", userId)
    userObj.first()
    .then (userobject) ->
        if _.isEmpty userobject
            result = 
                "message" : 'User does not exist' 
                "status" : '404'
            response.success result 
        else
            if userobject.get "password" == password
                result = 
                    "message" : 'User successfuly Logged in ' 
                    "status" : '201'
            else
                result = 
                    "message" : 'Invalid login details' 
                    "status" : '404'

            promise.resolve result
            , (error) ->
                promise.resolve error

    , (error) ->
        response.error error
    
    
Parse.Cloud.define 'createMissedResponse', (request, response) ->

    scheduleQuery = new Parse.Query('Schedule')
    scheduleQuery.notEqualTo("patient", '(undefined)')
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
            console.log newDateTime
            console.log currentDateTime
            console.log diffrence
            if(diffrence>1)
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
                    response.error error

        # save all responses
        Parse.Object.saveAll responseSaveArr
            .then (resObjs) ->
                # update all schedule nextoccurrence
                Parse.Object.saveAll scheduleSaveArr
                    .then (scheduleObjs) ->
                        response.success scheduleObjs
                    , (error) ->
                        response.error (error)   
            , (error) ->
                response.error (error)

    , (error) ->
        response.error error


Parse.Cloud.job 'createMissedResponse', (request, response) ->

    scheduleQuery = new Parse.Query('Schedule')
    scheduleQuery.notEqualTo("patient", '(undefined)')
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
            if(diffrence>1)
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
                    response.error error

        # save all responses
        Parse.Object.saveAll responseSaveArr
            .then (resObjs) ->
                # update all schedule nextoccurrence
                Parse.Object.saveAll scheduleSaveArr
                    .then (scheduleObjs) ->
                        response.success scheduleObjs
                    , (error) ->
                        response.error (error)   
            , (error) ->
                response.error (error)

    , (error) ->
        response.error error

getQuestionnaireFrequency =  ( questionnaireObj ) ->
    promise = new Parse.Promise()

    questionnaireScheduleQuery = new Parse.Query('Schedule')
    questionnaireScheduleQuery.equalTo("questionnaire", questionnaireObj)
    questionnaireScheduleQuery.first()
    .then (questionnaireScheduleObj) ->
        promise.resolve questionnaireScheduleObj.get("frequency")
    , (error) ->
        promise.resolve error

    promise


