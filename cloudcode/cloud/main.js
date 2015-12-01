(function() {
  var _, addResponse, getAnswer, getAnswers, getHospitalData, getPreviousAnswer, getQuestion, getoptions, storeDeviceData;

  Parse.Cloud.define("addHospital", function(request, response) {
    var hospitalObj;
    hospitalObj = new Parse.Object("Hospital");
    hospitalObj.set('name', request.params.hospitalName);
    hospitalObj.set('address', request.params.address);
    hospitalObj.set('primary_contact_number', request.params.primary_contact_number);
    hospitalObj.set('primary_email_address', request.params.primary_email_address);
    hospitalObj.set('website', request.params.website);
    hospitalObj.set('logo', request.params.logo);
    hospitalObj.set('contact_person_name', request.params.contact_person_name);
    hospitalObj.set('contact_person_email', request.params.contact_person_email);
    hospitalObj.set('contact_person_number', request.params.contact_person_number);
    return hospitalObj.save().then(function(hospitalObj) {
      return response.success(hospitalObj);
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("listHospitals", function(request, response) {
    var hospitalQuery;
    hospitalQuery = new Parse.Query("Hospital");
    return hospitalQuery.find().then(function(hospitalObjs) {
      var hospitalArray, hospitalData, hospitalObj, j, len;
      hospitalArray = [];
      hospitalData = function(hospitalObj) {
        var hospital;
        hospital = {};
        hospital['name'] = hospitalObj.get('name');
        hospital['address'] = hospitalObj.get('address');
        hospital['primary_contact_number'] = hospitalObj.get('primary_contact_number');
        hospital['primary_email_address'] = hospitalObj.get('primary_email_address');
        hospital['no_of_patients'] = "to be added";
        hospital['no_of_users'] = "to be added";
        hospital['no_of_doctors'] = "to be added";
        hospital['no_of_flags'] = "to be added";
        hospital['no_of_projects'] = "to be added";
        return hospitalArray.push(hospital);
      };
      for (j = 0, len = hospitalObjs.length; j < len; j++) {
        hospitalObj = hospitalObjs[j];
        hospitalData(hospitalObj);
      }
      return response.success(hospitalArray);
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("updateHospital", function(request, response) {
    var hospitalQuery;
    hospitalQuery = new Parse.Query("Hospital");
    return hospitalQuery.get(request.params.hospitalId).then(function(hospitalObj) {
      hospitalObj.set('name', request.params.hospitalName);
      hospitalObj.set('address', request.params.address);
      hospitalObj.set('primary_contact_number', request.params.primary_contact_number);
      hospitalObj.set('primary_email_address', request.params.primary_email_address);
      hospitalObj.set('website', request.params.website);
      hospitalObj.set('logo', request.params.logo);
      hospitalObj.set('contact_person_name', request.params.contact_person_name);
      hospitalObj.set('contact_person_email', request.params.contact_person_email);
      hospitalObj.set('contact_person_number', request.params.contact_person_number);
      return hospitalObj.save().then(function(hospitalObj) {
        return response.success(hospitalObj);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("deleteHospital", function(request, response) {
    var hospitalQuery;
    hospitalQuery = new Parse.Query("Hospital");
    return hospitalQuery.get(request.params.hospitalId).then(function(hospitalObj) {
      return hospitalObj.destroy({});
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define('getQuestionnaire', function(request, response) {
    var hospitalId, patientId, projectId, projectObj;
    projectId = request.params.projectId;
    hospitalId = request.params.hospitalId;
    patientId = request.params.patientId;
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
          return addResponse(projectobject, hospitalId, patientId, questionnaireObject).then(function(responseObj) {
            var questions;
            questions = {};
            return getQuestion(questionnaireObject, patientId, [], responseObj.id).then(function(questionData) {
              result = {
                "id": questionnaireObject.id,
                "name": questionnaireObject.get('name'),
                "description": questionnaireObject.get('description'),
                "question": questionData,
                "response": responseObj.id
              };
              return response.success(result);
            }, function(error) {
              return response.error(error);
            });
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
    var patientId, questionIds, questionnaireId, questionnaireQuery, responseId;
    questionnaireId = request.params.questionnaireId;
    questionIds = request.params.questionIds;
    patientId = request.params.patientId;
    responseId = request.params.responseId;
    questionnaireQuery = new Parse.Query('Questionnaire');
    questionnaireQuery.equalTo("objectId", questionnaireId);
    return questionnaireQuery.first().then(function(questionnaireObject) {
      var questions;
      questions = {};
      return getQuestion(questionnaireObject, patientId, questionIds, responseId).then(function(questionData) {
        return response.success(questionData);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define('getQuestion', function(request, response) {
    var answer, patientId, questionId, questionQuery, responseId;
    responseId = request.params.responseId;
    questionId = request.params.questionId;
    patientId = request.params.patientId;
    answer = request.params.answer;
    questionQuery = new Parse.Query('Questions');
    questionQuery.equalTo("objectId", questionId);
    return questionQuery.first().then(function(questionObject) {
      var options, previousAnswer, questionPromise, result;
      result = {};
      if (!_.isEmpty(questionObject)) {
        options = getoptions(questionObject);
        answer = getAnswer(answer, responseId);
        previousAnswer = getPreviousAnswer(questionObject, patientId, responseId);
        questionPromise = [];
        questionPromise.push(answer);
        questionPromise.push(previousAnswer);
        questionPromise.push(options);
        return Parse.Promise.when(questionPromise).then(function() {
          var answerObj, previousAnswerObj, questionPromiseArr;
          questionPromiseArr = _.flatten(_.toArray(arguments));
          answerObj = questionPromiseArr[0];
          previousAnswerObj = questionPromiseArr[1];
          options = {};
          if (questionPromiseArr.length > 1) {
            options = questionPromiseArr.splice(2, questionPromiseArr.length - 1);
          }
          result = {
            "id": questionObject.id,
            "question": questionObject.get('question'),
            "type": questionObject.get('type'),
            "options": options,
            "answer": answerObj,
            "previousAnswer": previousAnswerObj
          };
          return response.success(result);
        }, function(error) {
          return response.error(error);
        });
      }
    }, function(error) {
      return response.error(error);
    });
  });

  getQuestion = function(questionnaireObject, patientId, questionIds, responseId) {
    var promise, questionQuery;
    promise = new Parse.Promise();
    questionQuery = new Parse.Query('Questions');
    questionQuery.equalTo("questionnaire", questionnaireObject);
    questionQuery.equalTo('isSubQuestion', 'no');
    questionQuery.notContainedIn("objectId", questionIds);
    questionQuery.first().then(function(questionObject) {
      var options, previousAnswer, questionPromise, result;
      result = {};
      if (!_.isEmpty(questionObject)) {
        options = getoptions(questionObject);
        previousAnswer = getPreviousAnswer(questionObject, patientId, responseId);
        questionPromise = [];
        questionPromise.push(previousAnswer);
        questionPromise.push(options);
        return Parse.Promise.when(questionPromise).then(function() {
          var previousAnswerObj, questionPromiseArr;
          questionPromiseArr = _.flatten(_.toArray(arguments));
          previousAnswerObj = questionPromiseArr[0];
          options = {};
          if (questionPromiseArr.length > 1) {
            options = questionPromiseArr.splice(1, questionPromiseArr.length - 1);
          }
          result = {
            "id": questionObject.id,
            "question": questionObject.get('question'),
            "type": questionObject.get('type'),
            "options": options,
            "previousAnswer": previousAnswerObj
          };
          return promise.resolve(result);
        }, function(error) {
          console.log("getQuestion option ERROR");
          return response.error(error);
        });
      } else {
        return promise.resolve(result);
      }
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  addResponse = function(projectObj, hospitalId, patientId, questionnaireObj) {
    var hospitalObj, hospitalQuery, promise;
    promise = new Parse.Promise();
    hospitalObj = {};
    hospitalQuery = new Parse.Query('Hospital');
    hospitalQuery.get(hospitalId).then(function(hospitalObj) {
      var Response, responseObj;
      Response = Parse.Object.extend('Response');
      responseObj = new Response();
      responseObj.set('patient', patientId);
      responseObj.set('project', projectObj);
      responseObj.set('hospital', hospitalObj);
      responseObj.set('questionnaire', questionnaireObj);
      return responseObj.save().then(function(responseObj) {
        return promise.resolve(responseObj);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getAnswer = function(answer, responseId) {
    var promise, responseQuery;
    responseQuery = new Parse.Query('Response');
    promise = new Parse.Promise();
    if (answer) {
      responseQuery.get(responseId).then(function(responseObj) {
        var answerQuery;
        answerQuery = new Parse.Query('Answer');
        answerQuery.equalTo("response", responseObj);
        return answerQuery.first().then(function(answerObj) {
          var result;
          result = {
            "id": answerObj.id,
            "option": answerObj.get('option').id,
            "value": answerObj.get('value')
          };
          return promise.resolve(result);
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    } else {
      promise.resolve({});
    }
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
        var subQuestion;
        if (!_.isUndefined(optionObject.get('subQuestion'))) {
          subQuestion = optionObject.get('subQuestion').id;
        } else {
          subQuestion = '';
        }
        return result = {
          "id": optionObject.id,
          "label": optionObject.get('label'),
          "score": optionObject.get('score'),
          "subQuestion": subQuestion
        };
      });
      return promise.resolve(options);
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getPreviousAnswer = function(questionObject, patientId, responseId) {
    var Response, answerQuery, promise, responseObj;
    promise = new Parse.Promise();
    Response = Parse.Object.extend('Response');
    responseObj = new Response();
    responseObj.id = responseId;
    answerQuery = new Parse.Query('Answer');
    answerQuery.equalTo("question", questionObject);
    answerQuery.equalTo("patient", patientId);
    answerQuery.notEqualTo("response", responseObj);
    answerQuery.descending();
    answerQuery.first().then(function(answerObjects) {
      var result;
      result = {};
      if (!_.isEmpty(answerObjects)) {
        result = {
          "id": answerObjects.id,
          "option": answerObjects.get('option'),
          "value": answerObjects.get('value')
        };
      }
      return promise.resolve(result);
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

  Parse.Cloud.define('getSummary', function(request, response) {
    var responseId, responseQuery;
    responseId = request.params.responseId;
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo("objectId", responseId);
    return responseQuery.first().then(function(responseObj) {
      var answerQuery;
      answerQuery = new Parse.Query('Answer');
      answerQuery.include("question");
      answerQuery.include("option");
      answerQuery.equalTo("response", responseObj);
      return answerQuery.find().then(function(answerObjects) {
        return response.success(getAnswers(answerObjects));
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  getAnswers = function(answerObjects) {
    var answerObj, answers, getUniqueQuestions, j, k, len, len1, results, results1;
    results = function(answerObj) {
      return {
        input: answerObj['answer'],
        question: answerObj['question'].get('question'),
        optionSelected: answerObj['optionsSelected'],
        val: answerObj['temp']
      };
    };
    answers = [];
    getUniqueQuestions = function(answerObj) {
      var answer, currentQuestion, i, index, obj, q, questions;
      currentQuestion = answerObj.get('question');
      questions = (function() {
        var j, len, results1;
        results1 = [];
        for (j = 0, len = answers.length; j < len; j++) {
          obj = answers[j];
          results1.push(obj['question']);
        }
        return results1;
      })();
      answer = {};
      if (currentQuestion.id !== ((function() {
        var j, len, results1;
        results1 = [];
        for (j = 0, len = questions.length; j < len; j++) {
          q = questions[j];
          if (q.id === currentQuestion.id) {
            results1.push(q.id);
          }
        }
        return results1;
      })())[0]) {
        answer['temp'] = ((function() {
          var j, len, results1;
          results1 = [];
          for (j = 0, len = questions.length; j < len; j++) {
            q = questions[j];
            if (q.id === currentQuestion.id) {
              results1.push(q);
            }
          }
          return results1;
        })())[0];
        answer["question"] = currentQuestion;
        answer["answer"] = answerObj.get('value');
        if (currentQuestion.get('type') === 'multi-choice') {
          answer['optionsSelected'] = [];
          answer['optionsSelected'].push(answerObj.get('option').get('label'));
        } else if (currentQuestion.get('type') === 'single-choice') {
          answer['optionsSelected'] = [];
          answer['optionsSelected'].push(answerObj.get('option').get('label'));
        }
        return answers.push(answer);
      } else if (currentQuestion.get('type') === 'multi-choice') {
        index = ((function() {
          var j, len, results1;
          results1 = [];
          for (i = j = 0, len = questions.length; j < len; i = ++j) {
            q = questions[i];
            if (currentQuestion.id === q.id) {
              results1.push(i);
            }
          }
          return results1;
        })())[0];
        return answers[index]['optionsSelected'].push(answerObj.get('option').get('label'));
      }
    };
    for (j = 0, len = answerObjects.length; j < len; j++) {
      answerObj = answerObjects[j];
      getUniqueQuestions(answerObj);
    }
    results1 = [];
    for (k = 0, len1 = answers.length; k < len1; k++) {
      answerObj = answers[k];
      results1.push(results(answerObj));
    }
    return results1;
  };

  Parse.Cloud.define("addAnswers", function(request, response) {
    var questionQuery;
    questionQuery = new Parse.Query('Question');
    questionQuery.get(request.params.question).then(function(questionObj) {
      var responseObj;
      responseObj = new Parse.Query("Response");
      responseObj.get(request.params.response).then(function(responseObj) {
        var optionQuery;
        optionQuery = new Parse.Query("Options");
        optionQuery.get(request.params.option).then(function(optionObj) {
          var answer;
          answer = new Parse.Object("Answer");
          answer.set('response', responseObj);
          answer.set('patient', request.params.patient);
          asnswer.set('question', questionObj);
          answer.set('option', optionObj);
          answer.set('value', request.params.value);
          return answer.save().then(function(answer) {
            return response.success(answer);
          });
        });
        return function(error) {
          return response.error(error);
        };
      });
      return function(error) {
        return response.error(error);
      };
    });
    return function(error) {
      return response.error(error);
    };
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
