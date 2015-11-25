(function() {
  var _, getHospitalData, getQuestion, getoptions, storeDeviceData;

  Parse.Cloud.define('getQuestionnaire', function(request, response) {
    var projectId, projectObj;
    projectId = request.params.projectId;
    projectObj = new Parse.Query('Project');
    projectObj.equalTo("objectId", projectId);
    return projectObj.first().then(function(projectobject) {
      var questionnaireQuery, result;
      if (_.isEmpty(projectobject)) {
        result = {
          "message": 'project does not exits',
          "code": 'invalid_project',
          "status": '404'
        };
        return response.success(result);
      } else {
        questionnaireQuery = new Parse.Query('Questionnaire');
        questionnaireQuery.equalTo("project", projectobject);
        return questionnaireQuery.first().then(function(questionnaireObject) {
          var questions;
          questions = {};
          return getQuestion(questionnaireObject, []).then(function(questionData) {
            result = {
              "id": questionnaireObject.id,
              "name": questionnaireObject.get('name'),
              "description": questionnaireObject.get('description'),
              "question": questionData
            };
            return response.success(result);
          }, function(error) {
            return response.error(error);
          });
        }, function(error) {
          return response.error(error);
        });
      }
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define('getNextQuestion', function(request, response) {
    var questionIds, questionnaireId, questionnaireQuery;
    questionnaireId = request.params.questionnaireId;
    questionIds = request.params.questionIds;
    questionnaireQuery = new Parse.Query('Questionnaire');
    questionnaireQuery.equalTo("objectId", questionnaireId);
    return questionnaireQuery.first().then(function(questionnaireObject) {
      var questions;
      questions = {};
      return getQuestion(questionnaireObject, questionIds).then(function(questionData) {
        return response.success(questionData);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define('getQuestion', function(request, response) {
    var questionIds, questionQuery, responseId;
    responseId = request.params.responseId;
    questionIds = request.params.questionIds;
    questionQuery = new Parse.Query('Question');
    questionQuery.equalTo("objectId", questionIds);
    return questionQuery.first().then(function(questionObject) {
      var options, result;
      result = {};
      if (!_.isEmpty(questionObject)) {
        options = {};
        return getoptions(questionObject).then(function(optionsData) {
          console.log("optionsData");
          options = optionsData;
          return result = {
            "id": questionObject.id,
            "question": questionObject.get('question'),
            "type": questionObject.get('type'),
            "options": options
          };
        });
      }
    }, function(error) {
      return response.error(error);
    });
  });

  getQuestion = function(questionnaireObject, questionIds) {
    var promise, questionQuery;
    promise = new Parse.Promise();
    questionQuery = new Parse.Query('Questions');
    questionQuery.equalTo("questionnaire", questionnaireObject);
    questionQuery.notContainedIn("objectId", questionIds);
    questionQuery.first().then(function(questionObject) {
      var options, result;
      result = {};
      if (!_.isEmpty(questionObject)) {
        options = {};
        return getoptions(questionObject).then(function(optionsData) {
          console.log("optionsData");
          options = optionsData;
          result = {
            "id": questionObject.id,
            "question": questionObject.get('question'),
            "type": questionObject.get('type'),
            "options": options
          };
          return promise.resolve(result);
        }, function(error) {
          console.log("getQuestion option ERROR");
          return response.error(error);
        });
      }
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getoptions = function(questionObject) {
    var optionsQuery, promise;
    promise = new Parse.Promise();
    optionsQuery = new Parse.Query('Options');
    optionsQuery.equalTo("question", questionObject);
    optionsQuery.find().then(function(optionObjects) {
      var options, result;
      result = {};
      options = _.map(optionObjects, function(optionObject) {
        return result = {
          "id": optionObject.id,
          "label": optionObject.get('label'),
          "score": optionObject.get('score')
        };
      });
      return promise.resolve(options);
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define('saveAnswer', function(request, response) {
    var Answer, AnswerData, Questions, Response, answer, answerPromise, options, patientId, promiseArr, question, questionId, responseId, responseObj, value;
    responseId = request.params.responseId;
    patientId = parseInt(request.params.patientId);
    questionId = request.params.questionId;
    options = request.params.options;
    value = request.params.value;
    promiseArr = [];
    if (!_.isEmpty(options)) {
      _.each(options, function(optionId) {
        var Answer, AnswerData, Options, Questions, Response, answer, answerPromise, option, question, responseObj;
        Options = Parse.Object.extend("Options");
        option = new Options();
        option.id = optionId;
        Response = Parse.Object.extend("Response");
        responseObj = new Response();
        responseObj.id = responseId;
        Questions = Parse.Object.extend("Questions");
        question = new Questions();
        question.id = questionId;
        AnswerData = {
          response: responseObj,
          patient: patientId,
          question: question,
          option: option,
          value: value
        };
        Answer = Parse.Object.extend("Answer");
        answer = new Answer();
        answer.set("response", responseObj);
        answer.set("patient", patientId);
        answer.set("question", question);
        answer.set("option", option);
        answer.set("value", value);
        answerPromise = answer.save();
        return promiseArr.push(answerPromise);
      });
    } else {
      Response = Parse.Object.extend("Response");
      responseObj = new Response();
      responseObj.id = responseId;
      Questions = Parse.Object.extend("Questions");
      question = new Questions();
      question.id = questionId;
      AnswerData = {
        response: responseObj,
        patient: patientId,
        question: question,
        value: value
      };
      Answer = Parse.Object.extend("Answer");
      answer = new Answer();
      answer.set("response", responseObj);
      answer.set("patient", patientId);
      answer.set("question", question);
      answer.set("value", value);
      answerPromise = answer.save();
      promiseArr.push(answerPromise);
    }
    return Parse.Promise.when(promiseArr).then(function() {
      return response.success("Saved");
    }, function(error) {
      return response.error(error);
    });
  });

  _ = require('underscore.js');

  Parse.Cloud.define('doSetup', function(request, response) {
    var deviceIdentifier, referenceCode, userObj;
    referenceCode = request.params.referenceCode;
    deviceIdentifier = request.params.deviceIdentifier;
    userObj = new Parse.Query(Parse.User);
    userObj.equalTo("referenceCode", referenceCode);
    return userObj.first().then(function(userobject) {
      var hospitalData, result, userDeviceQuery, userId;
      if (_.isEmpty(userobject)) {
        result = {
          "message": 'reference code does not match',
          "code": 'invalid_reference_code',
          "status": '404'
        };
        return response.success(result);
      } else {
        userId = userobject.id;
        hospitalData = {};
        getHospitalData(userobject).then(function(userHospitalData) {
          return hospitalData = userHospitalData;
        }, function(error) {
          return response.error(error);
        });
        userDeviceQuery = new Parse.Query('UserDevices');
        userDeviceQuery.equalTo("user", userobject);
        return userDeviceQuery.find().then(function(userDeviceObjects) {
          var deviceExist, userDeviceCount;
          userDeviceCount = userDeviceObjects.length;
          deviceExist = {};
          if (userDeviceCount === 0) {
            result = {
              "userId": userobject.id,
              "hospitalData": hospitalData,
              "message": 'do new setup',
              "code": 'new_setup',
              "status": '500'
            };
          } else {
            deviceExist = _.find(userDeviceObjects, function(userDeviceObject) {
              console.log(userDeviceObject.get('deviceIdentifier'));
              if (userDeviceObject.get('deviceIdentifier') === deviceIdentifier) {
                console.log(userDeviceObject);
                return userDeviceObject;
              }
            });
            if (!_.isEmpty(deviceExist)) {
              result = {
                "userId": userobject.id,
                "hospitalData": hospitalData,
                "message": 'Device exist',
                "code": 'do_login',
                "status": '200'
              };
            } else {
              result = {
                "userId": userobject.id,
                "hospitalData": hospitalData,
                "message": 'Device does not exist',
                "code": 'new_setup',
                "status": '404'
              };
            }
          }
          return response.success(result);
        }, function(error) {
          return response.error(error);
        });
      }
    }, function(error) {
      return response.error(error);
    });
  });

  getHospitalData = function(userobject) {
    var hospitalUserQuery, promise;
    promise = new Parse.Promise();
    hospitalUserQuery = new Parse.Query('HospitalUser');
    hospitalUserQuery.equalTo("user", userobject);
    hospitalUserQuery.include("hospital");
    hospitalUserQuery.include("hospital.group");
    hospitalUserQuery.first().then(function(hospitalUserObjects) {
      var result;
      result = {
        "id": hospitalUserObjects.id,
        "name": hospitalUserObjects.get('hospital').get('name'),
        "group": hospitalUserObjects.get('hospital').get('group').get('name')
      };
      return promise.resolve(result);
    }, function(error) {
      return promise.resolve(error);
    });
    return promise;
  };

  storeDeviceData = function(request, response) {
    var UserDevice, accessType, deviceIdentifier, deviceOS, deviceType, promise, userDevice;
    promise = new Parse.Promise();
    deviceType = request.params.deviceType;
    deviceIdentifier = request.params.deviceIdentifier;
    deviceOS = request.params.deviceOS;
    accessType = request.params.accessType;
    UserDevice = Parse.Object.extend('UserDevices');
    userDevice = new UserDevice();
    userDevice.set("deviceType", deviceType);
    userDevice.set("deviceIdentifier", deviceIdentifier);
    userDevice.set("deviceOS", deviceOS);
    userDevice.set("accessType", accessType);
    userDevice.save().then(function(userDeviceObj) {
      var result;
      result = {
        "id": userDeviceObj.id
      };
      return promise.resolve(result);
    }, function(error) {
      return promise.resolve(error);
    });
    return promise;
  };

  Parse.Cloud.define('resetPassword', function(request, response) {
    var newpassword, userId, userObj;
    userId = request.params.userId;
    newpassword = request.params.newpassword;
    userObj = new Parse.Query(Parse.User);
    userObj.equalTo("objectId", userId);
    return userObj.first().then(function(userobject) {
      var result;
      if (_.isEmpty(userobject)) {
        result = {
          "message": 'User does not exist',
          "status": '404'
        };
        return response.success(result);
      } else {
        userobject.set("password", newpassword);
        return userobject.save().then(function(userDeviceObj) {
          result = {
            "message": 'User password successfuly updated ',
            "status": '201'
          };
          return promise.resolve(result);
        }, function(error) {
          return promise.resolve(error);
        });
      }
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define('userLogin', function(request, response) {
    var password, referenceCode, userObj;
    referenceCode = request.params.referenceCode;
    password = request.params.password;
    userObj = new Parse.Query(Parse.User);
    userObj.equalTo("objectId", userId);
    return userObj.first().then(function(userobject) {
      var result;
      if (_.isEmpty(userobject)) {
        result = {
          "message": 'User does not exist',
          "status": '404'
        };
        return response.success(result);
      } else {
        if (userobject.get("password" === password)) {
          result = {
            "message": 'User successfuly Logged in ',
            "status": '201'
          };
        } else {
          result = {
            "message": 'Invalid login details',
            "status": '404'
          };
        }
        return promise.resolve(result, function(error) {
          return promise.resolve(error);
        });
      }
    }, function(error) {
      return response.error(error);
    });
  });

}).call(this);
