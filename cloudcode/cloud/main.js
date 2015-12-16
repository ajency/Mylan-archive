(function() {
  var Buffer, TokenRequest, TokenStorage, _, createNewUser, createResponse, firstQuestion, getAnswers, getCompletedObjects, getCurrentAnswer, getHospitalData, getMissedObjects, getNextQuestion, getPreviousQuestionnaireAnswer, getQuestionData, getQuestionnaireFrequency, getResumeObject, getStartObject, getSummary, getUpcomingObject, getValidPeriod, isValidMissedTime, isValidTime, isValidUpcomingTime, moment, restrictedAcl, saveAnswer, storeDeviceData,
    indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

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

  Parse.Cloud.define("pushNotification", function(request, response) {
    var installationQuery;
    installationQuery = new Parse.Query(Parse.Installation);
    installationQuery.equalTo('installationId', request.params.installationId);
    return Parse.Push.send({
      where: installationQuery,
      data: {
        alert: "First push message :-)"
      },
      success: function() {
        return response.success("Message pushed");
      },
      error: function(error) {
        return response.error(error);
      }
    });
  });

  Parse.Cloud.define("startQuestionnaire", function(request, response) {
    var patientId, questionnaireId, responseId, responseQuery;
    responseId = request.params.responseId;
    questionnaireId = request.params.questionnaireId;
    patientId = request.params.patientId;
    if ((responseId !== "") && (!_.isUndefined(responseId)) && (!_.isUndefined(questionnaireId)) && (!_.isUndefined(patientId))) {
      responseQuery = new Parse.Query("Response");
      return responseQuery.get(responseId).then(function(responseObj) {
        var answeredQuestions, questionQuery;
        if (responseObj.get('status') === 'Completed') {
          return response.error("questionnaire_submitted");
        } else {
          answeredQuestions = responseObj.get('answeredQuestions');
          questionQuery = new Parse.Query('Questions');
          questionQuery.include('nextQuestion');
          questionQuery.include('previousQuestion');
          if (answeredQuestions.length !== 0) {
            return questionQuery.get(answeredQuestions[answeredQuestions.length - 1]).then(function(questionObj) {
              return getNextQuestion(questionObj, []).then(function(nextQuestionObj) {
                if (!_.isEmpty(nextQuestionObj)) {
                  return getQuestionData(nextQuestionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
                    return response.success(questionData);
                  }, function(error) {
                    return response.error(error);
                  });
                } else {
                  return getSummary(responseObj).then(function(summaryObjects) {
                    var result;
                    result = {};
                    result['status'] = "saved_successfully";
                    result['summary'] = summaryObjects;
                    return response.success(result);
                  }, function(error) {
                    return response.error(error);
                  });
                }
              }, function(error) {
                return response.error(error);
              });
            }, function(error) {
              return response.error(error);
            });
          } else {
            return firstQuestion(questionnaireId).then(function(questionObj) {
              return getQuestionData(questionObj, responseObj, patientId).then(function(questionData) {
                return response.success(questionData);
              }, function(error) {
                return response.error(error);
              });
            }, function(error) {
              return response.error(error);
            });
          }
        }
      }, function(error) {
        return response.error(error);
      });
    } else if ((responseId === "") && (!_.isUndefined(questionnaireId)) && (!_.isUndefined(patientId))) {
      return createResponse(questionnaireId, patientId).then(function(responseObj) {
        responseObj.set('status', 'Started');
        return responseObj.save().then(function(responseObj) {
          return firstQuestion(questionnaireId).then(function(questionObj) {
            return getQuestionData(questionObj, responseObj, patientId).then(function(questionData) {
              return response.success(questionData);
            }, function(error) {
              return response.error(error);
            });
          }, function(error) {
            return response.error(error);
          });
        }, function(error) {
          return response.error(error);
        });
      }, function(error) {
        return response.error(error);
      });
    } else {
      return response.error("Invalid request.");
    }
  });

  firstQuestion = function(questionnaireId) {
    var promise, questionnaireQuery;
    promise = new Parse.Promise();
    questionnaireQuery = new Parse.Query("Questionnaire");
    questionnaireQuery.get(questionnaireId).then(function(questionnaireObj) {
      var questionsQuery;
      questionsQuery = new Parse.Query("Questions");
      questionsQuery.equalTo('questionnaire', questionnaireObj);
      return questionsQuery.find().then(function(questionsObjs) {
        var checkAll, checkIfFirstQuestion, questionObj;
        checkIfFirstQuestion = function(questionObj) {
          if (_.isUndefined(questionObj.get('previousQuestion')) && !questionObj.get('isChild')) {
            return true;
          } else {
            return false;
          }
        };
        checkAll = (function() {
          var j, len, results1;
          results1 = [];
          for (j = 0, len = questionsObjs.length; j < len; j++) {
            questionObj = questionsObjs[j];
            if (checkIfFirstQuestion(questionObj)) {
              results1.push(questionObj);
            } else {
              continue;
            }
          }
          return results1;
        })();
        return promise.resolve(checkAll[0]);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  createResponse = function(questionnaireId, patientId) {
    var promise, questionnaireQuery;
    promise = new Parse.Promise();
    questionnaireQuery = new Parse.Query("Questionnaire");
    questionnaireQuery.get(questionnaireId).then(function(questionnaireObj) {
      var responseObj;
      responseObj = new Parse.Object("Response");
      responseObj.set('patient', patientId);
      responseObj.set('hospital', questionnaireObj.get('hospital'));
      responseObj.set('project', questionnaireObj.get('project'));
      responseObj.set('questionnaire', questionnaireObj);
      responseObj.set('answeredQuestions', []);
      return responseObj.save().then(function(responseObj) {
        return promise.resolve(responseObj);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getCurrentAnswer = function(questionObj, responseObj) {
    var answerQuery, hasAnswer, options, promise;
    options = [];
    hasAnswer = {};
    promise = new Parse.Promise();
    answerQuery = new Parse.Query("Answer");
    answerQuery.equalTo('response', responseObj);
    answerQuery.equalTo('question', questionObj);
    answerQuery.descending('updatedAt');
    answerQuery.include('question');
    answerQuery.include('option');
    if (questionObj.get('type') === 'multi-choice') {
      answerQuery.find().then(function(answerObjs) {
        var answerObj, j, len;
        for (j = 0, len = answerObjs.length; j < len; j++) {
          answerObj = answerObjs[j];
          options.push(answerObj.get('option').id);
        }
        if (!_.isUndefined(answerObjs[0])) {
          hasAnswer['value'] = answerObjs[0].get('value');
          hasAnswer['date'] = answerObjs[0].get('updatedAt');
          hasAnswer['option'] = options;
        }
        return promise.resolve(hasAnswer);
      }, function(error) {
        return promise.reject(error);
      });
    } else {
      answerQuery.first().then(function(answerObj) {
        if (!_.isUndefined(answerObj)) {
          if (!_.isUndefined(answerObj.get('option'))) {
            options.push(answerObj.get('option').id);
          }
          hasAnswer['option'] = options;
          hasAnswer['value'] = answerObj.get('value');
          hasAnswer['date'] = answerObj.get('updatedAt');
        }
        return promise.resolve(hasAnswer);
      }, function(error) {
        return promise.reject(error);
      });
    }
    return promise;
  };

  getQuestionData = function(questionObj, responseObj, patientId) {
    var promise, questionData;
    promise = new Parse.Promise();
    questionData = {};
    questionData['responseId'] = responseObj.id;
    questionData['responseStatus'] = responseObj.get('status');
    questionData['questionId'] = questionObj.id;
    questionData['questionType'] = questionObj.get('type');
    questionData['question'] = questionObj.get('question');
    questionData['next'] = !_.isUndefined(questionObj.get('nextQuestion')) ? true : false;
    questionData['previous'] = !_.isUndefined(questionObj.get('previousQuestion')) ? true : false;
    questionData['options'] = [];
    questionData['hasAnswer'] = {};
    questionData['previousQuestionnaireAnswer'] = {};
    getPreviousQuestionnaireAnswer(questionObj, responseObj, patientId).then(function(previousQuestionnaireAnswer) {
      questionData['previousQuestionnaireAnswer'] = previousQuestionnaireAnswer;
      return getCurrentAnswer(questionObj, responseObj).then(function(hasAnswer) {
        var optionsQuery;
        questionData['hasAnswer'] = hasAnswer;
        if (questionObj.get('type') === 'single-choice' || questionObj.get('type') === 'multi-choice' || questionObj.get('type') === 'input') {
          optionsQuery = new Parse.Query("Options");
          optionsQuery.equalTo('question', questionObj);
          return optionsQuery.find().then(function(optionObjs) {
            var j, len, option, optionObj, options;
            options = [];
            for (j = 0, len = optionObjs.length; j < len; j++) {
              option = optionObjs[j];
              optionObj = {};
              optionObj['id'] = option.id;
              optionObj['option'] = option.get('label');
              optionObj['score'] = option.get('score');
              options.push(optionObj);
            }
            questionData['options'] = options;
            return promise.resolve(questionData);
          }, function(error) {
            return promise.reject(error);
          });
        } else {
          return promise.resolve(questionData);
        }
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define('getNextQuestion', function(request, response) {
    var options, questionId, responseId, responseQuery, value;
    responseId = request.params.responseId;
    questionId = request.params.questionId;
    options = request.params.options;
    value = request.params.value;
    responseQuery = new Parse.Query('Response');
    return responseQuery.get(responseId).then(function(responseObj) {
      var questionQuery;
      if (responseObj.get('status') === 'Completed') {
        return response.error("questionnaire_submitted.");
      } else {
        questionQuery = new Parse.Query('Questions');
        questionQuery.include('nextQuestion');
        questionQuery.include('previousQuestion');
        return questionQuery.get(questionId).then(function(questionObj) {
          return saveAnswer(responseObj, questionObj, options, value).then(function(answersArray) {
            return getNextQuestion(questionObj, options).then(function(nextQuestionObj) {
              if (!_.isEmpty(nextQuestionObj)) {
                return getQuestionData(nextQuestionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
                  return response.success(questionData);
                }, function(error) {
                  return response.error(error);
                });
              } else {
                return getSummary(responseObj).then(function(summaryObjects) {
                  var result;
                  result = {};
                  result['status'] = "saved_successfully";
                  result['summary'] = summaryObjects;
                  return response.success(result);
                }, function(error) {
                  return response.error(error);
                });
              }
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

  getNextQuestion = function(questionObj, option) {
    var getRequiredQuestion, optionsQuery, promise;
    promise = new Parse.Promise();
    getRequiredQuestion = function() {
      var reponseObj;
      console.log("getQuestionData");
      if (!_.isUndefined(questionObj.get('nextQuestion'))) {
        return promise.resolve(questionObj.get('nextQuestion'));
      } else if (_.isUndefined(questionObj.get('nextQuestion') && !responObj.get(isChild))) {
        return promise.resolve({});
      } else {
        while (responObj.get(isChild) || !_.isUndefined(responseObj.get(previousQuestion))) {
          reponseObj = reponseObj.get(previousQuestion);
        }
        if (!_.isUndefined(questionObj.get('nextQuestion'))) {
          return promise.resolve(questionObj.get('nextQuestion'));
        } else {
          return promise.resolve({});
        }
      }
    };
    if (questionObj.get('type') === 'single-choice' && (!_.isUndefined(questionObj.get('condition')))) {
      optionsQuery = new Parse.Query("Options");
      optionsQuery.get(option[0]).then(function(optionObj) {
        var condition, conditionalQuestion, conditions, questionQuery;
        conditions = questionObj.get('condition');
        conditionalQuestion = (function() {
          var j, len, results1;
          results1 = [];
          for (j = 0, len = conditions.length; j < len; j++) {
            condition = conditions[j];
            if (condition['optionId'] === optionObj.id) {
              results1.push(condition['questionId']);
            }
          }
          return results1;
        })();
        if (conditionalQuestion.length !== 0) {
          questionQuery = new Parse.Query("Questions");
          return questionQuery.get(conditionalQuestion[0]).then(function(optionQuestionObj) {
            return promise.resolve(optionQuestionObj);
          }, function(error) {
            return promise.error(error);
          });
        } else {
          return getRequiredQuestion();
        }
      }, function(error) {
        return promise.error(error);
      });
    } else {
      getRequiredQuestion();
    }
    return promise;
  };

  getPreviousQuestionnaireAnswer = function(questionObject, responseObj, patientId) {
    var answerQuery, promise;
    promise = new Parse.Promise();
    answerQuery = new Parse.Query('Answer');
    answerQuery.equalTo("question", questionObject);
    answerQuery.equalTo("patient", patientId);
    answerQuery.notEqualTo("response", responseObj);
    answerQuery.descending('updatedAt');
    answerQuery.find().then(function(answerObjects) {
      var answerObj, first, optionIds, result;
      result = {};
      if (!_.isEmpty(answerObjects)) {
        optionIds = [];
        if (questionObject.get('type') === 'multi-choice') {
          first = answerObjects[0].get('response').id;
          optionIds = (function() {
            var j, len, results1;
            results1 = [];
            for (j = 0, len = answerObjects.length; j < len; j++) {
              answerObj = answerObjects[j];
              if (answerObj.get('response').id === first) {
                results1.push(answerObj.get('option').id);
              }
            }
            return results1;
          })();
        } else {
          if (!_.isUndefined(answerObjects[0])) {
            if (!_.isUndefined(answerObjects[0].get('option'))) {
              optionIds = [answerObjects[0].get('option').id];
            }
          }
        }
        result = {
          "optionId": optionIds,
          "value": answerObjects[0].get('value'),
          "date": answerObjects[0].updatedAt
        };
      }
      return promise.resolve(result);
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  saveAnswer = function(responseObj, questionObj, options, value) {
    var getAnswers, promise, promiseArr, responseObject;
    promiseArr = [];
    promise = new Parse.Promise();
    responseObject = {
      "__type": "Pointer",
      "className": "Response",
      "objectId": responseObj.id
    };
    getAnswers = function() {
      var answer, answerPromise;
      if (!_.isEmpty(options)) {
        _.each(options, function(optionId) {
          var optionQuery;
          optionQuery = new Parse.Query('Options');
          return optionQuery.get(optionId).then(function(optionObj) {
            var answer, answerPromise;
            answer = new Parse.Object('Answer');
            answer.set("response", responseObject);
            answer.set("patient", responseObj.get('patient'));
            answer.set("question", questionObj);
            answer.set("option", optionObj);
            answer.set("value", value);
            answerPromise = answer.save();
            promiseArr.push(answerPromise);
            return console.log("saved");
          }, function(error) {
            return promise.reject(error);
          });
        });
      } else {
        answer = new Parse.Object('Answer');
        answer.set("response", responseObj);
        answer.set("patient", responseObj.get('patient'));
        answer.set("question", questionObj);
        answer.set("value", value);
        answerPromise = answer.save();
        console.log("saved");
        promiseArr.push(answerPromise);
      }
      return Parse.Promise.when(promiseArr);
    };
    return getAnswers().then(function() {
      var answeredQuestions, ref;
      answeredQuestions = responseObj.get('answeredQuestions');
      if (ref = questionObj.id, indexOf.call(answeredQuestions, ref) < 0) {
        answeredQuestions.push(questionObj.id);
      }
      responseObj.set('answeredQuestions', answeredQuestions);
      return responseObj.save().then(function(responseObj) {
        return promise.resolve(responseObj);
      }, function(error) {
        return promise.error(error);
      });
    }, function(error) {
      return promise.error(error);
    });
  };

  Parse.Cloud.define("getPreviousQuestion", function(request, response) {
    var options, questionId, responseId, responseQuery, value;
    responseId = request.params.responseId;
    questionId = request.params.questionId;
    options = request.params.options;
    value = request.params.value;
    responseQuery = new Parse.Query('Response');
    responseQuery.include('questionnaire');
    return responseQuery.get(responseId).then(function(responseObj) {
      var questionQuery;
      if (responseObj.get('status') === 'Completed') {
        return response.error("questionnaire_submitted.");
      } else {
        questionQuery = new Parse.Query('Questions');
        questionQuery.include('previousQuestion');
        return questionQuery.get(questionId).then(function(questionObj) {
          if (!_.isEmpty(options) || value !== "") {
            return saveAnswer(responseObj, questionObj, options, value).then(function(answersArray) {
              if (_.isUndefined(questionObj.get('previousQuestion')) && !questionObj.get('isChild')) {
                return getQuestionData(questionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
                  return response.success(questionData);
                }, function(error) {
                  return response.error(error);
                });
              } else {
                return getQuestionData(questionObj.get('previousQuestion'), responseObj, responseObj.get('patient')).then(function(questionData) {
                  return response.success(questionData);
                }, function(error) {
                  return response.error(error);
                });
              }
            }, function(error) {
              return response.error(error);
            });
          } else {
            if (_.isUndefined(questionObj.get('previousQuestion')) && !questionObj.get('isChild')) {
              return getQuestionData(questionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
                return response.success(questionData);
              }, function(error) {
                return response.error(error);
              });
            } else {
              return getQuestionData(questionObj.get('previousQuestion'), responseObj, responseObj.get('patient')).then(function(questionData) {
                return response.success(questionData);
              }, function(error) {
                return response.error(error);
              });
            }
          }
        }, function(error) {
          return response.error(error);
        });
      }
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
      return getSummary(responseObj).then(function(answerObjects) {
        return response.success(answerObjects);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  getSummary = function(responseObj) {
    var answerQuery, promise;
    promise = new Parse.Promise();
    answerQuery = new Parse.Query('Answer');
    answerQuery.include("question");
    answerQuery.include("option");
    answerQuery.equalTo("response", responseObj);
    answerQuery.find().then(function(answerObjects) {
      return promise.resolve(getAnswers(answerObjects));
    }, function(error) {
      return promise.error(error);
    });
    return promise;
  };

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
        } else {
          answer['optionsSelected'] = [];
          if (!_.isUndefined(answerObj.get('option'))) {
            answer['optionsSelected'].push(answerObj.get('option').get('label'));
          }
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

  Parse.Cloud.define("submitQuestionnaire", function(request, response) {
    var responseId, responseQuery;
    responseId = request.params.responseId;
    responseQuery = new Parse.Query("Response");
    return responseQuery.get(responseId).then(function(responseObj) {
      responseObj.set("status", "Completed");
      return responseObj.save().then(function(responseObj) {
        return response.success("submitted_successfully");
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("dashboard", function(request, response) {
    var patientId, results, scheduleQuery;
    patientId = request.params.patientId;
    results = [];
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.equalTo('patient', patientId);
    scheduleQuery.include('questionnaire');
    return scheduleQuery.first().then(function(scheduleObj) {
      return getResumeObject(scheduleObj, patientId).then(function(resumeObj) {
        return getStartObject(scheduleObj, patientId).then(function(startObj) {
          return getUpcomingObject(scheduleObj, patientId).then(function(upcomingObj) {
            return getCompletedObjects(patientId).then(function(completedObj) {
              return getMissedObjects(scheduleObj, patientId).then(function(missedObj) {
                results.push(resumeObj);
                results.push(startObj);
                results.push(upcomingObj);
                results.push(completedObj);
                results.push(missedObj);
                return response.success(results);
              }, function(error) {
                return response.error(error);
              });
            }, function(error) {
              return response.error(error);
            });
          }, function(error) {
            return response.error(error);
          });
        }, function(error) {
          return response.error(error);
        });
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  getValidPeriod = function(scheduleObj) {
    var graceAfterOccurrence, graceBeforeOccurrence, gracePeriod, nextOccurrence, timeObj;
    nextOccurrence = scheduleObj.get('nextOccurrence');
    gracePeriod = scheduleObj.get('questionnaire').get('gracePeriod') * 1000;
    graceBeforeOccurrence = new Date(nextOccurrence.getTime());
    graceBeforeOccurrence.setTime(graceBeforeOccurrence.getTime() - gracePeriod);
    graceAfterOccurrence = new Date(nextOccurrence.getTime());
    graceAfterOccurrence.setTime(graceAfterOccurrence.getTime() + gracePeriod);
    timeObj = {};
    timeObj['graceBeforeOccurrence'] = graceBeforeOccurrence;
    timeObj['graceAfterOccurrence'] = graceAfterOccurrence;
    return timeObj;
  };

  isValidTime = function(timeObj) {
    var currentTime;
    currentTime = new Date();
    if ((timeObj['graceBeforeOccurrence'].getTime() <= currentTime.getTime()) && (timeObj['graceAfterOccurrence'].getTime() >= currentTime.getTime())) {
      return true;
    } else {
      return false;
    }
  };

  getStartObject = function(scheduleObj, patientId) {
    var promise, responseQuery, responseQuery1, responseQuery2, startObj, timeObj;
    startObj = {};
    promise = new Parse.Promise();
    timeObj = getValidPeriod(scheduleObj);
    if (isValidTime(timeObj)) {
      responseQuery1 = new Parse.Query('Response');
      responseQuery1.equalTo('status', 'Started');
      responseQuery2 = new Parse.Query('Response');
      responseQuery2.equalTo('status', 'Completed');
      responseQuery = Parse.Query.or(responseQuery1, responseQuery2);
      responseQuery.equalTo('patient', patientId);
      responseQuery.greaterThanOrEqualTo('createdAt', timeObj['graceBeforeOccurrence']);
      responseQuery.lessThanOrEqualTo('createdAt', timeObj['graceAfterOccurrence']);
      responseQuery.first().then(function(responseObj) {
        if (!_.isUndefined(responseObj)) {
          startObj['status'] = "not_start";
        } else {
          startObj['status'] = "start";
        }
        return promise.resolve(startObj);
      }, function(error) {
        return promise.error(error);
      });
    } else {
      startObj['status'] = "not_start";
      promise.resolve(startObj);
    }
    return promise;
  };

  getUpcomingObject = function(scheduleObj, patientId) {
    var promise, timeObj, upcomingObj;
    upcomingObj = {};
    promise = new Parse.Promise();
    timeObj = getValidPeriod(scheduleObj);
    if (isValidUpcomingTime(timeObj)) {
      upcomingObj['status'] = "upcoming";
      promise.resolve(upcomingObj);
    } else {
      upcomingObj['status'] = "not_upcoming";
      promise.resolve(upcomingObj);
    }
    return promise;
  };

  isValidUpcomingTime = function(timeObj) {
    var currentTime;
    currentTime = new Date();
    if (timeObj['graceBeforeOccurrence'].getTime() > currentTime.getTime()) {
      return true;
    } else {
      return false;
    }
  };

  getCompletedObjects = function(patientId) {
    var completedObj, promise, responseQuery;
    completedObj = {};
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('patient', patientId);
    responseQuery.equalTo('status', 'Completed');
    responseQuery.descending('createdAt');
    responseQuery.find().then(function(responseObjs) {
      var responseObj;
      completedObj['status'] = "completed";
      completedObj['responseIds'] = (function() {
        var j, len, results1;
        results1 = [];
        for (j = 0, len = responseObjs.length; j < len; j++) {
          responseObj = responseObjs[j];
          results1.push(responseObj.id);
        }
        return results1;
      })();
      return promise.resolve(completedObj);
    }, function(error) {
      return promise.error(error);
    });
    return promise;
  };

  getResumeObject = function(scheduleObj, patientId) {
    var promise, responseQuery, resumeObj, timeObj;
    resumeObj = {};
    promise = new Parse.Promise();
    timeObj = getValidPeriod(scheduleObj);
    if (isValidTime(timeObj)) {
      responseQuery = new Parse.Query('Response');
      responseQuery.equalTo('patient', patientId);
      responseQuery.equalTo('status', 'Started');
      responseQuery.greaterThanOrEqualTo('createdAt', timeObj['graceBeforeOccurrence']);
      responseQuery.lessThanOrEqualTo('createdAt', timeObj['graceAfterOccurrence']);
      responseQuery.first().then(function(responseObj) {
        if (!_.isUndefined(responseObj)) {
          resumeObj['status'] = "resume";
          resumeObj['responseId'] = responseObj.id;
        } else {
          resumeObj['status'] = "not_resume";
          resumeObj['responseId'] = "";
        }
        return promise.resolve(resumeObj);
      }, function(error) {
        return promise.error(error);
      });
    } else {
      resumeObj['status'] = "not_resume";
      resumeObj['responseId'] = "";
      promise.resolve(resumeObj);
    }
    return promise;
  };

  getMissedObjects = function(scheduleObj, patientId) {
    var missedObj, promise, responseQuery, timeObj;
    missedObj = {};
    promise = new Parse.Promise();
    timeObj = getValidPeriod(scheduleObj);
    if (!isValidMissedTime(timeObj)) {
      missedObj['status'] = "not_missed";
      missedObj['responseId'] = "";
      promise.resolve(missedObj);
    } else {
      responseQuery = new Parse.Query('Response');
      responseQuery.equalTo('patient', patientId);
      responseQuery.greaterThanOrEqualTo('createdAt', timeObj['graceBeforeOccurrence']);
      responseQuery.first().then(function(responseObj) {
        if (!_.isUndefined(responseObj) && responseObj.get('status') === 'Completed') {
          missedObj['status'] = "not_missed";
          missedObj['responseId'] = "";
          return promise.resolve(missedObj);
        } else if (!_.isUndefined(responseObj) && responseObj.get('status') === 'missed') {
          missedObj['status'] = "missed";
          missedObj['responseId'] = responseObj.id;
          return promise.resolve(missedObj);
        } else if (!_.isUndefined(responseObj) && responseObj.get('status') === 'Started') {
          responseObj.set('status', 'missed');
          return responseObj.save().then(function(responseObj) {
            missedObj['status'] = "missed";
            missedObj['responseId'] = responseObj.id;
            return promise.resolve(missedObj);
          }, function(error) {
            return promise.error(error);
          });
        } else if (_.isUndefined(responseObj)) {
          return createResponse(scheduleObj.get('questionnaire').id, patientId).then(function(responseObj) {
            responseObj.set('status', 'missed');
            return responseObj.save().then(function(responseObj) {
              missedObj['status'] = "missed";
              missedObj['responseId'] = responseObj.id;
              return promise.resolve(missedObj);
            }, function(error) {
              return promise.error(error);
            });
          }, function(error) {
            return promise.error(error);
          });
        }
      }, function(error) {
        return promise.error(error);
      });
    }
    return promise;
  };

  isValidMissedTime = function(timeObj) {
    var currentTime;
    currentTime = new Date();
    if (timeObj['graceAfterOccurrence'].getTime() < currentTime.getTime()) {
      return true;
    } else {
      return false;
    }
  };

  _ = require('underscore.js');

  moment = require('cloud/moment.js');

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

  Parse.Cloud.define('createMissedResponse', function(request, response) {
    var scheduleQuery;
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.notEqualTo("patient", '(undefined)');
    scheduleQuery.include("questionnaire");
    return scheduleQuery.find().then(function(scheduleObjects) {
      var responseSaveArr, result, scheduleSaveArr;
      result = {};
      responseSaveArr = [];
      scheduleSaveArr = [];
      _.each(scheduleObjects, function(scheduleObject) {
        var Response, currentDateTime, diffrence, gracePeriod, newDateTime, nextOccurrence, patient, questionnaire, responseData, responseObj;
        questionnaire = scheduleObject.get("questionnaire");
        patient = scheduleObject.get("patient");
        gracePeriod = questionnaire.get("gracePeriod");
        nextOccurrence = moment(scheduleObject.get("nextOccurrence"));
        newDateTime = moment(nextOccurrence).add(gracePeriod, 's');
        currentDateTime = moment();
        diffrence = moment(newDateTime).diff(currentDateTime);
        console.log(newDateTime);
        console.log(currentDateTime);
        console.log(diffrence);
        if (diffrence > 1) {
          responseData = {
            patient: patient,
            questionnaire: questionnaire,
            status: 'missed',
            schedule: scheduleObject
          };
          Response = Parse.Object.extend("Response");
          responseObj = new Response(responseData);
          responseSaveArr.push(responseObj);
          return getQuestionnaireFrequency(questionnaire).then(function(frequency) {
            var date, newNextOccurrence;
            frequency = parseInt(frequency);
            newNextOccurrence = moment(nextOccurrence).add(frequency, 's');
            date = new Date(newNextOccurrence);
            scheduleObject.set('nextOccurrence', date);
            return scheduleSaveArr.push(scheduleObject);
          }, function(error) {
            return response.error(error);
          });
        }
      });
      return Parse.Object.saveAll(responseSaveArr).then(function(resObjs) {
        return Parse.Object.saveAll(scheduleSaveArr).then(function(scheduleObjs) {
          return response.success(scheduleObjs);
        }, function(error) {
          return response.error(error);
        });
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.job('createMissedResponse', function(request, response) {
    var scheduleQuery;
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.notEqualTo("patient", '(undefined)');
    scheduleQuery.include("questionnaire");
    return scheduleQuery.find().then(function(scheduleObjects) {
      var responseSaveArr, result, scheduleSaveArr;
      result = {};
      responseSaveArr = [];
      scheduleSaveArr = [];
      _.each(scheduleObjects, function(scheduleObject) {
        var Response, currentDateTime, diffrence, diffrence2, gracePeriod, newDateTime, nextOccurrence, patient, questionnaire, responseData, responseObj;
        questionnaire = scheduleObject.get("questionnaire");
        patient = scheduleObject.get("patient");
        gracePeriod = questionnaire.get("gracePeriod");
        nextOccurrence = moment(scheduleObject.get("nextOccurrence"));
        newDateTime = moment(nextOccurrence).add(gracePeriod, 's');
        currentDateTime = moment();
        diffrence = moment(newDateTime).diff(currentDateTime);
        diffrence2 = moment(currentDateTime).diff(newDateTime);
        console.log(newDateTime);
        console.log(currentDateTime);
        console.log(diffrence);
        console.log(diffrence2);
        if (diffrence > 1) {
          responseData = {
            patient: patient,
            questionnaire: questionnaire,
            status: 'missed',
            schedule: scheduleObject
          };
          Response = Parse.Object.extend("Response");
          responseObj = new Response(responseData);
          responseSaveArr.push(responseObj);
          return getQuestionnaireFrequency(questionnaire).then(function(frequency) {
            var date, newNextOccurrence;
            frequency = parseInt(frequency);
            newNextOccurrence = moment(nextOccurrence).add(frequency, 's');
            date = new Date(newNextOccurrence);
            scheduleObject.set('nextOccurrence', date);
            return scheduleSaveArr.push(scheduleObject);
          }, function(error) {
            return response.error(error);
          });
        }
      });
      return Parse.Object.saveAll(responseSaveArr).then(function(resObjs) {
        return Parse.Object.saveAll(scheduleSaveArr).then(function(scheduleObjs) {
          return response.success(scheduleObjs);
        }, function(error) {
          return response.error(error);
        });
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  getQuestionnaireFrequency = function(questionnaireObj) {
    var promise, questionnaireScheduleQuery;
    promise = new Parse.Promise();
    questionnaireScheduleQuery = new Parse.Query('Schedule');
    questionnaireScheduleQuery.equalTo("questionnaire", questionnaireObj);
    questionnaireScheduleQuery.first().then(function(questionnaireScheduleObj) {
      return promise.resolve(questionnaireScheduleObj.get("frequency"));
    }, function(error) {
      return promise.resolve(error);
    });
    return promise;
  };

  Buffer = require('buffer').Buffer;

  TokenRequest = Parse.Object.extend("TokenRequest");

  TokenStorage = Parse.Object.extend("TokenStorage");

  restrictedAcl = new Parse.ACL();

  restrictedAcl.setPublicReadAccess(false);

  restrictedAcl.setPublicWriteAccess(false);

  Parse.Cloud.define('loginParseUser', function(request, response) {
    var authKey, installationId, querySchedule, referenceCode, scheduleFlag;
    authKey = request.params.authKey;
    referenceCode = String(request.params.referenceCode);
    installationId = String(request.params.installationId);
    scheduleFlag = false;
    querySchedule = new Parse.Query("Schedule");
    querySchedule.equalTo('patient', referenceCode);
    return querySchedule.first().then(function(scheduleObj) {
      var queryTokenStorage;
      if (!_.isEmpty(scheduleObj)) {
        scheduleFlag = true;
      }
      queryTokenStorage = new Parse.Query("TokenStorage");
      queryTokenStorage.equalTo('referenceCode', referenceCode);
      queryTokenStorage.equalTo('installationId', installationId);
      return queryTokenStorage.first({
        useMasterKey: true
      }).then(function(tokenStorageObj) {
        var appData, querySession, storedAuthKey, user;
        if (_.isEmpty(tokenStorageObj)) {
          appData = {
            installationId: installationId,
            referenceCode: referenceCode
          };
          return createNewUser(authKey, appData).then(function(user) {
            var result;
            result = {
              sessionToken: user.getSessionToken(),
              scheduleFlag: scheduleFlag
            };
            return response.success(result);
          }, function(error) {
            return response.error(error);
          });
        } else {
          storedAuthKey = tokenStorageObj.get("authKey");
          user = tokenStorageObj.get("user");
          if (storedAuthKey === authKey) {
            querySession = new Parse.Query(Parse.Session);
            querySession.equalTo('user', user);
            return querySession.first({
              useMasterKey: true
            }).then(function(sessionObj) {
              var result, sessionToken;
              sessionToken = sessionObj.get('sessionToken');
              result = {
                sessionToken: sessionToken,
                scheduleFlag: scheduleFlag
              };
              return response.success(result);
            }, function(error) {
              return response.error(error);
            });
          } else {
            tokenStorageObj.set("authKey", authKey);
            return tokenStorageObj.save().then(function(newTokenStorageObj) {
              querySession = new Parse.Query(Parse.Session);
              querySession.equalTo('user', user);
              return querySession.first({
                useMasterKey: true
              }).then(function(sessionObj) {
                var result, sessionToken;
                sessionToken = sessionObj.get('sessionToken');
                result = {
                  sessionToken: sessionToken,
                  scheduleFlag: scheduleFlag
                };
                return response.success(result);
              }, function(error) {
                return response.error(error);
              });
            }, function(error) {
              return response.error(error);
            });
          }
        }
      });
    }, function(error) {
      return response.error(error);
    });
  });

  createNewUser = function(authKey, appData) {
    var password, promise, user, username;
    promise = new Parse.Promise();
    user = new Parse.User;
    username = new Buffer(24);
    password = new Buffer(24);
    _.times(24, function(i) {
      username.set(i, _.random(0, 255));
      return password.set(i, _.random(0, 255));
    });
    user.set('username', username.toString('base64'));
    user.set('password', password.toString('base64'));
    user.signUp().then(function(user) {
      var ts;
      ts = new TokenStorage();
      ts.set('authKey', authKey);
      ts.set('installationId', appData.installationId);
      ts.set('referenceCode', appData.referenceCode);
      ts.set('user', user);
      ts.setACL(restrictedAcl);
      return ts.save(null, {
        useMasterKey: true
      }).then(function(TokenStorageObj) {
        return promise.resolve(user);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

}).call(this);
