(function() {
  var Buffer, TokenRequest, TokenStorage, _, checkMissedResponses, convertToZone, createMissedResponse, createNewUser, createResponse, cronjobRunTime, deleteAllAnswers, deleteDependentQuestions, firstQuestion, getAllNotifications, getAnswers, getBaseLineScores, getBaseLineValues, getCompletedObjects, getCurrentAnswer, getFlag, getHospitalData, getLastQuestion, getMissedObjects, getNextQuestion, getNotificationMessage, getNotificationSendObject, getNotificationType, getNotifications, getPatientNotifications, getPatientsAnswers, getPreviousQuestion, getPreviousQuestionnaireAnswer, getPreviousScores, getPreviousValues, getQuestionData, getQuestionnaireFrequency, getResumeObject, getSequence, getStartObject, getSummary, getUpcomingObject, getValidPeriod, getValidTimeFrame, hasSeenNotification, isLateSubmission, isValidMissedTime, isValidTime, isValidUpcomingTime, listAllAnswersForPatient, listAllAnswersForProject, listAllResponsesForPatient, listAllResponsesForProject, moment, momenttimezone, restrictedAcl, saveAnswer, saveAnswer1, saveDescriptive, saveInput, saveMultiChoice, saveSingleChoice, sendNotifications, storeDeviceData, timeZoneConverter, updateMissedObjects,
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

  cronjobRunTime = 60;

  Parse.Cloud.define("testNotifications", function(request, response) {
    return getNotifications().then(function() {
      return sendNotifications().then(function() {
        var convertedDate, currentDate;
        currentDate = moment().format();
        convertedDate = convertToZone(currentDate, 'Asia/Calcutta').format();
        return response.success("moment = " + currentDate + " date = " + convertedDate);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  getNotifications = function() {
    var promise, scheduleQuery;
    promise = new Parse.Promise();
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.exists('patient');
    scheduleQuery.include('questionnaire');
    scheduleQuery.find().then(function(scheduleObjs) {
      var getAllNotifications;
      getAllNotifications = function() {
        var promise1;
        promise1 = Parse.Promise.as();
        _.each(scheduleObjs, function(scheduleObj) {
          return promise1 = promise1.then(function() {
            return scheduleObj.fetch().then(function() {
              return getNotificationType(scheduleObj).then(function(notificationType) {
                var dummy, notificationObj;
                if (notificationType !== "") {
                  notificationObj = new Parse.Object('Notification');
                  notificationObj.set('hasSeen', false);
                  notificationObj.set('patient', scheduleObj.get('patient'));
                  notificationObj.set('type', notificationType);
                  notificationObj.set('processed', false);
                  notificationObj.set('schedule', scheduleObj);
                  notificationObj.set('cleared', false);
                  notificationObj.set('occurrenceDate', scheduleObj.get('nextOccurrence'));
                  return notificationObj.save();
                } else {
                  dummy = new Parse.Promise();
                  dummy.resolve();
                  return dummy;
                }
              }, function(error) {
                return promise.reject(error);
              });
            }, function(error) {
              return promise1.reject(error);
            });
          }, function(error) {
            return promise1.reject(error);
          });
        });
        return promise1;
      };
      return getAllNotifications().then(function() {
        return promise.resolve();
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getNotificationType = function(scheduleObj) {
    var promise, questionnaireQuery;
    promise = new Parse.Promise();
    questionnaireQuery = new Parse.Query('Questionnaire');
    questionnaireQuery.get(scheduleObj.get('questionnaire').id);
    questionnaireQuery.first().then(function(questionnaireObj) {
      var afterReminder, beforeReminder, bufferTime, currentDate, graceDate, nextOccurrence, reminderTime;
      nextOccurrence = scheduleObj.get('nextOccurrence');
      currentDate = new Date();
      graceDate = new Date(scheduleObj.get('nextOccurrence').getTime() + (questionnaireObj.get('gracePeriod') * 1000));
      reminderTime = questionnaireObj.get('reminderTime');
      beforeReminder = new Date(nextOccurrence.getTime() - reminderTime * 1000);
      afterReminder = new Date(nextOccurrence.getTime() + reminderTime * 1000);
      bufferTime = cronjobRunTime / 2;
      if ((currentDate.getTime() >= (nextOccurrence.getTime() - (reminderTime * 1000) - (bufferTime * 1000))) && (currentDate.getTime() <= (nextOccurrence.getTime() - (reminderTime * 1000) + (bufferTime * 1000)))) {
        return promise.resolve("beforOccurrence");
      } else if ((currentDate.getTime() >= (graceDate.getTime() - (reminderTime * 1000) - (bufferTime * 1000))) && (currentDate.getTime() <= (graceDate.getTime() - (reminderTime * 1000) + (bufferTime * 1000)))) {
        return promise.resolve("beforeGracePeriod");
      } else {
        return promise.resolve("");
      }
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getNotificationMessage = function(scheduleObj, notificationType, occurrenceDate, installationId) {
    var promise, questionnaireQuery;
    promise = new Parse.Promise();
    questionnaireQuery = new Parse.Query('Questionnaire');
    questionnaireQuery.get(scheduleObj.get('questionnaire').id);
    questionnaireQuery.first().then(function(questionnaireObj) {
      return timeZoneConverter(installationId, occurrenceDate).then(function(convertedTimezoneObject) {
        var convertedGraceDate, graceDate, gracePeriod, message, newNextOccurrence, timeZone;
        newNextOccurrence = convertedTimezoneObject['occurrenceDate'];
        timeZone = convertedTimezoneObject['timeZone'];
        console.log("**New newNextOccurrence**");
        console.log(newNextOccurrence);
        gracePeriod = questionnaireObj.get("gracePeriod");
        graceDate = moment(occurrenceDate).add(gracePeriod, 's').format();
        if (timeZone !== '') {
          convertedGraceDate = momenttimezone.tz(graceDate, timeZone).format('DD-MM-YYYY hh:mm A');
        } else {
          convertedGraceDate = graceDate;
        }
        console.log("convertedGraceDate");
        console.log(convertedGraceDate);
        if (notificationType === "beforOccurrence") {
          message = "Questionnaire is due on " + newNextOccurrence;
        } else if (notificationType === "beforeGracePeriod") {
          message = "Questionnairre was due on " + newNextOccurrence + ". Please submit it by " + convertedGraceDate;
        } else if (notificationType === "missedOccurrence") {
          message = "You have missed the questionnaire due on " + newNextOccurrence;
        } else {
          message = "";
        }
        console.log("**Notification Msg occurrenceDate**");
        console.log(occurrenceDate);
        return promise.resolve(message);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  sendNotifications = function() {
    var Arr, notificationQuery, promise;
    Arr = [];
    promise = new Parse.Promise();
    notificationQuery = new Parse.Query('Notification');
    notificationQuery.equalTo('processed', false);
    notificationQuery.find().then(function(notifications) {
      getNotifications = function() {
        var promise1;
        promise1 = Parse.Promise.as();
        _.each(notifications, function(notification) {
          return promise1 = promise1.then(function() {
            notification.set('processed', true);
            return notification.save().then(function(notification) {
              var scheduleQuery;
              scheduleQuery = new Parse.Query('Schedule');
              scheduleQuery.equalTo('patient', notification.get('patient'));
              return scheduleQuery.first().then(function(scheduleObj) {
                var tokenStorageQuery;
                tokenStorageQuery = new Parse.Query('TokenStorage');
                tokenStorageQuery.equalTo('referenceCode', notification.get('patient'));
                return tokenStorageQuery.find({
                  useMasterKey: true
                }).then(function(tokenStorageObjs) {
                  var sendToInstallations;
                  sendToInstallations = function() {
                    var promise2;
                    promise2 = Parse.Promise.as();
                    _.each(tokenStorageObjs, function(tokenStorageObj) {
                      return promise2 = promise2.then(function() {
                        return getNotificationMessage(scheduleObj, notification.get('type'), notification.get('occurrenceDate'), tokenStorageObj.get('installationId')).then(function(message) {
                          var installationQuery;
                          console.log("message " + message);
                          console.log("---------------------");
                          installationQuery = new Parse.Query(Parse.Installation);
                          installationQuery.equalTo('installationId', tokenStorageObj.get('installationId'));
                          installationQuery.limit(1);
                          installationQuery.find();
                          return Parse.Push.send({
                            where: installationQuery,
                            data: {
                              id: notification.id,
                              header: "Mylan",
                              message: message
                            }
                          });
                        }, function(error) {
                          return promise1.reject(error);
                        });
                      }, function(error) {
                        console.log("send1");
                        return promise2.reject(error);
                      });
                    });
                    return promise2;
                  };
                  return sendToInstallations();
                }, function(error) {
                  console.log("send2");
                  return promise1.reject(error);
                });
              }, function(error) {
                console.log("send3");
                return promise1.reject(error);
              });
            }, function(error) {
              console.log("send4");
              return promise1.reject(error);
            });
          }, function(error) {
            console.log("send5");
            return promise1.reject(error);
          });
        });
        return promise1;
      };
      return getNotifications().then(function() {
        return promise.resolve(Arr);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define("createMissedResponse", function(request, response) {
    return createMissedResponse().then(function(responses) {
      return response.success(responses);
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define('deleteResponse', function(request, response) {
    var responseQuery;
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('sequenceNumber', 101);
    responseQuery.equalTo('status', 'missed');
    return responseQuery.find().then(function(responses) {
      var getResponse;
      getResponse = function() {
        var promise;
        promise = Parse.Promise.as();
        _.each(responses, function(responseObj) {
          return promise = promise.then(function() {
            console.log("111");
            return responseObj.destroy();
          });
        });
        return promise;
      };
      return getResponse().then(function() {
        return response.success("done");
      });
    }, function(error) {
      return response.error(error);
    });
  });

  checkMissedResponses = function() {
    var promise, responseQuery;
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('status', 'started');
    responseQuery.include('questionnaire');
    responseQuery.include('schedule');
    responseQuery.find().then(function(responseObjs) {
      var updateResponses;
      updateResponses = function() {
        var promise1;
        promise1 = Parse.Promise.as();
        _.each(responseObjs, function(responseObj) {
          return promise1 = promise1.then(function() {
            return getValidTimeFrame(responseObj.get('questionnaire'), responseObj.get('occurrenceDate')).then(function(timeObj) {
              var currentDateTime, notificationObj;
              currentDateTime = moment().format();
              if (moment(currentDateTime).isAfter(timeObj['upperLimit'], 'second')) {
                notificationObj = new Parse.Object('Notification');
                notificationObj.set('hasSeen', false);
                notificationObj.set('patient', responseObj.get('patient'));
                notificationObj.set('type', 'missedOccurrence');
                notificationObj.set('processed', false);
                notificationObj.set('schedule', responseObj.get('schedule'));
                notificationObj.set('cleared', false);
                notificationObj.set('occurrenceDate', responseObj.get('occurrenceDate'));
                return notificationObj.save().then(function(notificationObj) {
                  responseObj.set('status', 'missed');
                  return responseObj.save();
                });
              } else {
                return responseObj.save();
              }
            }, function(error) {
              return promise.reject(error);
            });
          }, function(error) {
            return promise.reject(error);
          });
        });
        return promise1;
      };
      return updateResponses().then(function() {
        return promise.resolve("done");
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  createMissedResponse = function() {
    var promise, scheduleQuery;
    promise = new Parse.Promise();
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.exists('patient');
    scheduleQuery.include('questionnaire');
    scheduleQuery.find().then(function(scheduleObjs) {
      var updateMissedResponse;
      updateMissedResponse = function() {
        var promise1;
        promise1 = Parse.Promise.as();
        _.each(scheduleObjs, function(scheduleObj) {
          return promise1 = promise1.then(function() {
            return getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence')).then(function(timeObj) {
              var currentDateTime;
              currentDateTime = moment().format();
              if (moment(currentDateTime).isAfter(timeObj['upperLimit'], 'second')) {
                return createResponse(scheduleObj.get('questionnaire').id, scheduleObj.get('patient'), scheduleObj).then(function(responseObj) {
                  responseObj.set('occurrenceDate', scheduleObj.get('nextOccurrence'));
                  responseObj.set('status', 'missed');
                  return responseObj.save().then(function(responseObj) {
                    var notificationObj;
                    notificationObj = new Parse.Object('Notification');
                    notificationObj.set('hasSeen', false);
                    notificationObj.set('patient', scheduleObj.get('patient'));
                    notificationObj.set('type', 'missedOccurrence');
                    notificationObj.set('processed', false);
                    notificationObj.set('schedule', scheduleObj);
                    notificationObj.set('cleared', false);
                    notificationObj.set('occurrenceDate', scheduleObj.get('nextOccurrence'));
                    return notificationObj.save().then(function(notificationObj) {
                      scheduleQuery = new Parse.Query('Schedule');
                      scheduleQuery.doesNotExist('patient');
                      scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'));
                      return scheduleQuery.first().then(function(scheduleQuestionnaireObj) {
                        var newNextOccurrence;
                        newNextOccurrence = new Date(scheduleObj.get('nextOccurrence').getTime());
                        newNextOccurrence.setTime(newNextOccurrence.getTime() + Number(scheduleQuestionnaireObj.get('frequency')) * 1000);
                        scheduleObj.set('nextOccurrence', newNextOccurrence);
                        return scheduleObj.save();
                      }, function(error) {
                        console.log("missed1");
                        return promise1.reject(error);
                      });
                    }, function(error) {
                      return promise1.reject(error);
                    });
                  }, function(error) {
                    console.log("missed2");
                    return promise1.reject(error);
                  });
                }, function(error) {
                  console.log("missed3");
                  return promise1.reject(error);
                });
              } else {
                return scheduleObj.save();
              }
            }, function(error) {
              return promise1.reject(error);
            });
          }, function(error) {
            console.log("missed4");
            return promise1.reject(error);
          });
        });
        return promise1;
      };
      return updateMissedResponse().then(function() {
        return promise.resolve("done");
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.job('commonJob', function(request, response) {
    console.log("==================");
    return checkMissedResponses().then(function(result) {
      console.log("result = " + result);
      return createMissedResponse().then(function(responses) {
        console.log("responses = " + responses.length);
        return getNotifications().then(function(notifications) {
          console.log("notifications = " + notifications);
          return sendNotifications().then(function(notifications) {
            console.log("notifications_sent = " + notifications);
            console.log(new Date());
            return response.success("job_run");
          }, function(error) {
            return response.error("not_run");
          });
        }, function(error) {
          return promise.reject(error);
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

  Parse.Cloud.define("getAllNotifications", function(request, response) {
    var notificationMessages, page, patientId;
    patientId = request.params.patientId;
    notificationMessages = [];
    page = 0;
    return getAllNotifications(patientId, notificationMessages, page).then(function(notifications) {
      return response.success(notifications);
    }, function(error) {
      return response.error(error);
    });
  });

  getAllNotifications = function(patientId, notificationMessages, page) {
    var limit, notificationQuery, promise;
    limit = 50;
    promise = new Parse.Promise();
    notificationQuery = new Parse.Query('Notification');
    notificationQuery.equalTo('patient', patientId);
    notificationQuery.equalTo('cleared', false);
    notificationQuery.include('schedule');
    notificationQuery.limit(limit);
    notificationQuery.skip(page * limit);
    notificationQuery.find().then(function(notifications) {
      var getAll, j, len, notification;
      if (!_.isEmpty(notifications)) {
        for (j = 0, len = notifications.length; j < len; j++) {
          notification = notifications[j];
          notificationMessages.push(notification);
        }
        page++;
        getAllNotifications(patientId, notificationMessages, page);
      }
      getAll = function() {
        var promise1;
        promise1 = Parse.Promise.as();
        _.each(notificationMessages, function(notification) {
          return promise1 = promise1.then(function() {
            return getNotificationSendObject(notification.get('schedule'), notification).then(function(notificationSendObject) {
              var dummy;
              notificationMessages.push(notificationSendObject);
              dummy = new Parse.Promise();
              dummy.resolve();
              return dummy;
            }, function(error) {
              return promise1.reject(error);
            });
          }, function(error) {
            return promise1.reject(error);
          });
        });
        return promise1;
      };
      return getAll().then(function() {
        return promise.resolve(notificationMessages);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define("getPatientNotificationCount", function(request, response) {
    var notificationQuery, patientId;
    patientId = request.params.patientId;
    notificationQuery = new Parse.Query('Notification');
    notificationQuery.equalTo('patient', patientId);
    notificationQuery.equalTo('cleared', false);
    return notificationQuery.count().then(function(notificationCount) {
      return response.success(notificationCount);
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("getPatientNotifications", function(request, response) {
    var limit, page, patientId;
    patientId = request.params.patientId;
    page = request.params.page;
    limit = request.params.limit;
    return getPatientNotifications(patientId, page, limit).then(function(notifications) {
      return response.success(notifications);
    }, function(error) {
      return response.error(error);
    });
  });

  getPatientNotifications = function(patientId, page, limit) {
    var notificationQuery, promise;
    promise = new Parse.Promise();
    notificationQuery = new Parse.Query('Notification');
    notificationQuery.equalTo('patient', patientId);
    notificationQuery.equalTo('cleared', false);
    notificationQuery.include('schedule');
    notificationQuery.limit(limit);
    notificationQuery.skip(page * limit);
    notificationQuery.find().then(function(notifications) {
      var getAll, notificationMessages;
      notificationMessages = [];
      getAll = function() {
        var promise1;
        promise1 = Parse.Promise.as();
        _.each(notifications, function(notification) {
          return promise1 = promise1.then(function() {
            return getNotificationSendObject(notification.get('schedule'), notification).then(function(notificationSendObject) {
              var dummy;
              notificationMessages.push(notificationSendObject);
              dummy = new Parse.Promise();
              dummy.resolve();
              return dummy;
            }, function(error) {
              return promise1.reject(error);
            });
          }, function(error) {
            return promise1.reject(error);
          });
        });
        return promise1;
      };
      return getAll().then(function() {
        return promise.resolve(notificationMessages);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getNotificationSendObject = function(scheduleObj, notification) {
    var promise, questionnaireQuery;
    promise = new Parse.Promise();
    questionnaireQuery = new Parse.Query('Questionnaire');
    questionnaireQuery.get(scheduleObj.get('questionnaire').id);
    questionnaireQuery.first().then(function(questionnaireObj) {
      var graceDate, notificationSendObject, notificationType, occurrenceDate;
      occurrenceDate = notification.get('occurrenceDate');
      graceDate = new Date(scheduleObj.get('nextOccurrence').getTime() + (questionnaireObj.get('gracePeriod') * 1000));
      notificationType = notification.get('type');
      notificationSendObject = {};
      notificationSendObject['occurrenceDate'] = occurrenceDate;
      notificationSendObject['graceDate'] = graceDate;
      notificationSendObject['id'] = notification.id;
      notificationSendObject['hasSeen'] = notification.get('hasSeen');
      if (notificationType === "beforOccurrence") {
        notificationSendObject['type'] = "beforOccurrence";
        return promise.resolve(notificationSendObject);
      } else if (notificationType === "beforeGracePeriod") {
        notificationSendObject['type'] = "beforeGracePeriod";
        return promise.resolve(notificationSendObject);
      } else if (notificationType === "missedOccurrence") {
        notificationSendObject['type'] = "missedOccurrence";
        return promise.resolve(notificationSendObject);
      } else {
        return promise.resolve(notificationSendObject);
      }
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define("hasSeenNotification", function(request, response) {
    var notificationId;
    notificationId = request.params.notificationId;
    return hasSeenNotification(notificationId).then(function(notification) {
      return response.success("hasSeen");
    }, function(error) {
      return response.error(error);
    });
  });

  hasSeenNotification = function(notificationId) {
    var notificationQuery, promise;
    promise = new Parse.Promise();
    notificationQuery = new Parse.Query('Notification');
    notificationQuery.get(notificationId).then(function(notification) {
      notification.set('hasSeen', true);
      return notification.save().then(function(notification) {
        return promise.resolve(notification);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define('timeZoneConverter', function(request, response) {
    return timeZoneConverter('cb11e368-67d6-4df4-b79e-0be25cfe5577', '2016-02-01T08:11:44.000Z').then(function(time) {
      return response.success(time);
    }, function(error) {
      return response.error(error);
    });
  });

  timeZoneConverter = function(installationId, occurrenceDate) {
    var convertedTimezoneObject, installationQuery, promise;
    momenttimezone.tz.add("Asia/Calcutta|HMT BURT IST IST|-5R.k -6u -5u -6u|01232|-18LFR.k 1unn.k HB0 7zX0");
    momenttimezone.tz.link("Asia/Calcutta|Asia/Kolkata");
    convertedTimezoneObject = {};
    promise = new Parse.Promise();
    installationQuery = new Parse.Query(Parse.Installation);
    installationQuery.equalTo('installationId', installationId);
    installationQuery.first({
      useMasterKey: true
    }).then(function(installationObj) {
      var convertedTime, timeZone;
      if (!_.isEmpty(installationObj)) {
        console.log("******converted******");
        timeZone = installationObj.get("timeZone");
        convertedTime = momenttimezone.tz(occurrenceDate, timeZone).format('DD-MM-YYYY hh:mm A');
        console.log(installationId);
        console.log(convertedTime);
        console.log("******converted******");
        convertedTimezoneObject['occurrenceDate'] = convertedTime;
        convertedTimezoneObject['timeZone'] = timeZone;
        return promise.resolve(convertedTimezoneObject);
      } else {
        console.log("******Not converted******");
        convertedTimezoneObject['occurrenceDate'] = occurrenceDate;
        convertedTimezoneObject['timeZone'] = '';
        return promise.resolve(convertedTimezoneObject);
      }
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  convertToZone = function(timeObj, timezone) {
    var convertedTime;
    console.log("****timeObj****");
    console.log(timeObj);
    convertedTime = momenttimezone.tz(timeObj, timezone);
    console.log(convertedTime);
    return convertedTime;
  };

  Parse.Cloud.define("clearNotification", function(request, response) {
    var notificationId, notificationQuery;
    notificationId = request.params.notificationId;
    notificationQuery = new Parse.Query('Notification');
    return notificationQuery.get(notificationId).then(function(notification) {
      notification.set('cleared', true);
      return notification.save().then(function(notification) {
        return response.success("cleared");
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("clearAllNotifications", function(request, response) {
    var notificationQuery, patientId;
    patientId = request.params.patientId;
    notificationQuery = new Parse.Query('Notification');
    notificationQuery.equalTo('cleared', false);
    return notificationQuery.find().then(function(notifications) {
      var notificationSaveArr;
      notificationSaveArr = [];
      _.each(notifications, function(notification) {
        notification.set('cleared', true);
        return notificationSaveArr.push(notification);
      });
      return Parse.Object.saveAll(notificationSaveArr).then(function(notificationObjs) {
        return response.success(notificationObjs);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("clearNotificationByIds", function(request, response) {
    var notificationIds, notificationQuery;
    notificationIds = request.params.notificationIds;
    notificationQuery = new Parse.Query('Notification');
    notificationQuery.containedIn('objectId', notificationIds);
    notificationQuery.equalTo('cleared', false);
    return notificationQuery.find().then(function(notifications) {
      var notificationSaveArr;
      notificationSaveArr = [];
      _.each(notifications, function(notification) {
        notification.set('cleared', true);
        return notificationSaveArr.push(notification);
      });
      return Parse.Object.saveAll(notificationSaveArr).then(function(notificationObjs) {
        return response.success(notificationObjs);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });


  /*
  createMissedResponse = () ->
  	promise = new Parse.Promise()
  	scheduleQuery = new Parse.Query('Schedule')
  	scheduleQuery.exists('patient')
  	scheduleQuery.include('questionnaire')
  	scheduleQuery.find()
  	.then (scheduleObjs) ->
  		updateMissedResponse = ->
  			promise1 = []
  			_.each scheduleObjs, (scheduleObj) ->
  				timeObj = getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence'))
  				currentDate = new Date()
  				if currentDate.getTime() > timeObj['upperLimit'].getTime()
  					createResponse(scheduleObj.get('questionnaire').id, scheduleObj.get('patient'), scheduleObj)
  					#.then (responseObj) ->
  					responseObj = new Parse.Object "Response"
  					responseObj.set 'patient', scheduleObj.get('patient')
  					responseObj.set 'hospital', scheduleObj.get('questionnaire').get('hospital')
  					responseObj.set 'project', scheduleObj.get('questionnaire').get('project')
  					responseObj.set 'questionnaire', scheduleObj.get('questionnaire')
  					responseObj.set 'schedule', scheduleObj
  					responseObj.set 'occurrenceDate', scheduleObj.get('nextOccurrence')
  					responseObj.set 'status', 'missed'
  					responseObj.save()
  					.then (responseObj) ->
  						console.log "-------------------------------"
  						console.log  responseObj.id
  						console.log scheduleObj.id
  						console.log "-----------------------------"
  						scheduleQuery = new Parse.Query('Schedule')
  						scheduleQuery.doesNotExist('patient')
  						scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'))
  						scheduleQuery.first()
  						.then (scheduleQuestionnaireObj) ->
  							newNextOccurrence = new Date (scheduleObj.get('nextOccurrence').getTime())
  							newNextOccurrence.setTime(newNextOccurrence.getTime() + Number(scheduleQuestionnaireObj.get('frequency')) * 1000)
  							console.log "-------------"
  							console.log "------#{scheduleObj.get('nextOccurrence')}"
  							console.log "======#{newNextOccurrence}"
  							console.log "-------------"
  							scheduleObj.set 'nextOccurrence', newNextOccurrence
  							promise1.push(scheduleObj.save())
  						#, (error) ->
  							 *	promise1.reject error
  						#, (error) ->
  						 *	promise1.reject (error)
  					#, (error) ->
  					 *	promise1.reject error
  			Parse.Promise.when(promise1)
  		updateMissedResponse()
  		.then () ->
  			promise.resolve("done")
  		, (error) ->
  			promise.reject error
  	, (error) ->
  		promise.reject error
  	promise
  
  
  
  
  
  
  
  Parse.Cloud.define "createMissedResponse1", (request, response) ->
  	getNotifications()
  	.then (notifications) ->
  		console.log  "done1"
  		sendNotifications() 
  		.then (notifications) ->
  			console.log  "done2"
  			checkMissedResponses()
  			.then (result) ->
  				console.log  "done3"
  				createMissedResponse()
  				.then (responses) ->
  					console.log  (new Date())
  					response.success "job_run"
  				, (error) ->
  					response.error error
  			, (error) ->
  				promise.reject error				
  		, (error) ->
  			response.error error
  	, (error) ->
  		response.error error
  
  
  
  
  
  
  
  createMissedResponse1 = () ->
  	promise = new Parse.Promise()
  	scheduleQuery = new Parse.Query('Schedule')
  	scheduleQuery.exists("patient")
  	scheduleQuery.include("questionnaire")
  	scheduleQuery.find()
  	.then (scheduleObjects) ->
  		result ={}
  		responseSaveArr =[]
  		scheduleSaveArr =[]
  		_.each scheduleObjects, (scheduleObject) ->
  			questionnaire = scheduleObject.get("questionnaire")
  			patient = scheduleObject.get("patient")
  			gracePeriod = questionnaire.get("gracePeriod")
  			nextOccurrence =  moment(scheduleObject.get("nextOccurrence"))
  			newDateTime = moment(nextOccurrence).add(gracePeriod, 's')
  			currentDateTime = moment()
  			diffrence = moment(newDateTime).diff(currentDateTime)
  			diffrence2 = moment(currentDateTime).diff(newDateTime)
  			if(parseInt(diffrence2) > 1)
  				responseQuery = new Parse.Query('Response')
  				responseQuery.equalTo('questionnaire', scheduleObject.get('questionnaire'))
  				responseQuery.equalTo('patient', scheduleObject.get('patient'))
  				responseQuery.descending('occurrenceDate')
  				responseQuery.notEqualTo('status', 'base_line')
  				responseQuery.find()
  				.then (responseObjs) ->
  					responseData=
  						patient: patient
  						questionnaire: questionnaire
  						status : 'missed'
  						schedule : scheduleObject
  						sequenceNumber : responseObjs.length + 1
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
  						promise.reject error
  				, (error) ->
  					promise.reject error
  		 * save all responses
  		Parse.Object.saveAll responseSaveArr
  			.then (resObjs) ->
  				 * update all schedule nextoccurrence
  				Parse.Object.saveAll scheduleSaveArr
  					.then (scheduleObjs) ->
  						promise.resolve scheduleObjs
  					, (error) ->
  						promise.reject (error)   
  			, (error) ->
  				promise.reject (error)
  	, (error) ->
  		promise.reject error
  
  	promise
   */


  /*
  console.log "======================================="
  	console.log "nextOccurrence = #{nextOccurrence}"
  	console.log "beforeReminder =#{beforeReminder}"
  	console.log "afterReminder =#{afterReminder}"
  	console.log "currentDate = #{currentDate}"
  	console.log "graceDate = #{graceDate}"
  	console.log "rem = #{reminderTime}"
  	console.log "=============================================="
   */

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


  /*
  sendNotifications = () ->
  	promise = new Parse.Promise()
  	notificationQuery = new Parse.Query('Notification')
  	notificationQuery.equalTo('processed', false)
  	notificationQuery.find()
  	.then (notifyObjs) ->
  		promise1 = Parse.Promise.as()
  		_.each notifyObjs, (notifyObj) ->
  			promise1 = promise1
  			.then () ->
  				patientId = notifyObjs.get('patient') 
  				tokenStorageQuery = new Parse.Query('TokenStorage')
  				tokenStorageQuery.equalTo('referenceCode', patientId)
  				tokenStorageQuery.find()
  				.then (tokenStorageObjs) ->
  					promise2 = Parse.Promise.as()
  					_.each tokenStorageObjs, (tokenStorageObj) ->
  						promise2 = promise2
  						.then () ->
  							installationQuery = new Parse.Query('Installation')
  							installationQuery.equalTo(tokenStorageObj.get('installationId'))
  							Parse.Push.send({
  								where: installationQuery
  								data: {alert: getNotificationMessage(notifyObj.get('type'))}
  								})
  							.then () ->
  								notifyObj. set 'processed', true
  								notifyObj.save()
  							, (error) ->
  								promise2.reject error
  						, (error) ->
  							promise2.reject error
  					promise2
  				, (error)
  					promise1.reject error
  			, (error) ->
  				promise1.reject error
  		promise1
  	, (error) ->
  		promise.reject error
  	promise
   */

  Parse.Cloud.define("startQuestionnaire", function(request, response) {
    var patientId, questionnaireId, responseId, responseQuery, scheduleQuery;
    responseId = request.params.responseId;
    questionnaireId = request.params.questionnaireId;
    patientId = request.params.patientId;
    if ((responseId !== "") && (!_.isUndefined(responseId)) && (!_.isUndefined(questionnaireId)) && (!_.isUndefined(patientId))) {
      responseQuery = new Parse.Query("Response");
      return responseQuery.get(responseId).then(function(responseObj) {
        var answeredQuestions, questionQuery, result;
        if (responseObj.get('status') !== 'started') {
          result = {};
          result['status'] = responseObj.get('status');
          return response.success(result);
        } else {
          answeredQuestions = responseObj.get('answeredQuestions');
          questionQuery = new Parse.Query('Questions');
          questionQuery.include('nextQuestion');
          questionQuery.include('previousQuestion');
          questionQuery.include('questionnaire');
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
                  result = {};
                  result['status'] = "saved_successfully";
                  return response.success(result);
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
      scheduleQuery = new Parse.Query('Schedule');
      scheduleQuery.equalTo('patient', patientId);
      return scheduleQuery.first().then(function(scheduleObj) {
        return createResponse(questionnaireId, patientId, scheduleObj).then(function(responseObj) {
          responseObj.set('reviewed', 'unreviewed');
          responseObj.set('status', 'started');
          responseObj.set('occurrenceDate', scheduleObj.get('nextOccurrence'));
          return responseObj.save().then(function(responseObj) {
            scheduleQuery = new Parse.Query('Schedule');
            scheduleQuery.doesNotExist('patient');
            scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'));
            return scheduleQuery.first().then(function(scheduleQuestionnaireObj) {
              var newNextOccurrence;
              newNextOccurrence = moment(scheduleObj.get('nextOccurrence')).add(scheduleQuestionnaireObj.get('frequency'), 's').format();
              newNextOccurrence = new Date(newNextOccurrence);
              scheduleObj.set('nextOccurrence', newNextOccurrence);
              return scheduleObj.save().then(function(scheduleObj) {
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
      questionsQuery.include('questionnaire');
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
    var promise;
    promise = new Parse.Promise();
    questionObj.fetch().then(function() {
      var editable, questionData;
      questionData = {};
      questionData['responseId'] = responseObj.id;
      questionData['responseStatus'] = responseObj.get('status');
      questionData['questionId'] = questionObj.id;
      questionData['questionType'] = questionObj.get('type');
      questionData['question'] = questionObj.get('question');
      questionData['previous'] = !_.isUndefined(questionObj.get('previousQuestion')) ? true : false;
      questionData['options'] = [];
      questionData['hasAnswer'] = {};
      questionData['previousQuestionnaireAnswer'] = {};
      questionData['questionTitle'] = questionObj.get('title');
      questionData['editable'] = {};
      questionData['isChild'] = questionObj.get('isChild');
      editable = questionObj.get('questionnaire');
      return editable.fetch().then(function() {
        questionData['editable'] = editable.get('editable');
        return getPreviousQuestionnaireAnswer(questionObj, responseObj, patientId).then(function(previousQuestionnaireAnswer) {
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
                return getNextQuestion(questionObj, []).then(function(value) {
                  console.log("-------------");
                  console.log("if questionData");
                  console.log(value);
                  console.log("--------------");
                  if (!_.isEmpty(value)) {
                    questionData['next'] = true;
                  } else {
                    questionData['next'] = false;
                  }
                  return promise.resolve(questionData);
                }, function(error) {
                  return promise.reject(error);
                });
              }, function(error) {
                return promise.reject(error);
              });
            } else {
              return getNextQuestion(questionObj, []).then(function(value) {
                console.log("-------------");
                console.log("else questionData");
                console.log(value);
                console.log("--------------");
                if (!_.isEmpty(value)) {
                  questionData['next'] = true;
                } else {
                  questionData['next'] = false;
                }
                return promise.resolve(questionData);
              }, function(error) {
                return promise.reject(error);
              });
            }
          }, function(error) {
            return promise.reject(error);
          });
        }, function(error) {
          return promise.reject(error);
        });
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
      var questionQuery, result;
      if (responseObj.get('status') !== 'started') {
        result = {};
        result['status'] = responseObj.get('status');
        return response.success(result);
      } else {
        questionQuery = new Parse.Query('Questions');
        questionQuery.include('nextQuestion');
        questionQuery.include('previousQuestion');
        questionQuery.include('questionnaire');
        return questionQuery.get(questionId).then(function(questionObj) {
          return saveAnswer(responseObj, questionObj, options, value).then(function(answers) {
            return getNextQuestion(questionObj, options).then(function(nextQuestionObj) {
              if (!_.isEmpty(nextQuestionObj)) {
                return getQuestionData(nextQuestionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
                  return response.success(questionData);
                }, function(error) {
                  return response.error(error);
                });
              } else {
                result = {};
                result['status'] = "saved_successfully";
                return response.success(result);
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
      var questionQuery;
      if (!_.isUndefined(questionObj.get('nextQuestion'))) {
        return promise.resolve(questionObj.get('nextQuestion'));
      } else if ((_.isUndefined(questionObj.get('nextQuestion'))) && !questionObj.get('isChild')) {
        return promise.resolve({});
      } else {
        questionQuery = new Parse.Query('Questions');
        questionQuery.include('previousQuestion');
        return questionQuery.get(questionObj.id).then(function(questionObj) {
          while (questionObj.get('isChild')) {
            console.log("=========================");
            console.log(questionObj);
            console.log("=========================");
            questionObj = questionObj.get('previousQuestion');
          }
          return questionObj.fetch().then(function() {
            if (!_.isUndefined(questionObj.get('nextQuestion'))) {
              console.log("*****************************************");
              console.log(questionObj.get('nextQuestion').id);
              return promise.resolve(questionObj.get('nextQuestion'));
            } else {
              console.log(questionObj.id);
              console.log("xxxxxxxxxxxxxxxxxxxxxxx");
              return promise.resolve({});
            }
          }, function(error) {
            return promise.reject(error);
          });
        }, function(error) {
          return promise.reject(error);
        });
      }
    };
    if (questionObj.get('type') === 'single-choice' && (!_.isUndefined(questionObj.get('condition'))) && !_.isEmpty(option)) {
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
          questionQuery.include('questionnaire');
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


  /*
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
   */

  Parse.Cloud.define("getPreviousQuestion", function(request, response) {
    var last, options, questionId, responseId, responseQuery, value;
    responseId = request.params.responseId;
    last = questionId = request.params.questionId;
    options = request.params.options;
    value = request.params.value;
    responseQuery = new Parse.Query('Response');
    responseQuery.include('questionnaire');
    return responseQuery.get(responseId).then(function(responseObj) {
      var questionQuery, result;
      if (responseObj.get('status') !== 'started') {
        result = {};
        result['status'] = responseObj.get('status');
        return response.success(result);
      } else if (questionId === "") {
        console.log("=================");
        return getLastQuestion(responseObj).then(function(questionObj) {
          console.log("questionObj " + questionObj);
          return getQuestionData(questionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
            console.log("questionData " + questionData);
            console.log("-------------------------");
            return response.success(questionData);
          }, function(error) {
            return response.error(error);
          });
        }, function(error) {
          return response.error(error);
        });
      } else {
        questionQuery = new Parse.Query('Questions');
        questionQuery.include('previousQuestion');
        questionQuery.include('questionnaire');
        return questionQuery.get(questionId).then(function(questionObj) {
          if (!_.isEmpty(options) || (value !== "")) {
            return saveAnswer(responseObj, questionObj, options, value).then(function(answersArray) {
              return getPreviousQuestion(questionObj, responseObj).then(function(questionObj) {
                return getQuestionData(questionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
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
          } else {
            console.log("=================");
            return getPreviousQuestion(questionObj, responseObj).then(function(questionObj) {
              console.log("questionObj " + questionObj);
              return getQuestionData(questionObj, responseObj, responseObj.get('patient')).then(function(questionData) {
                console.log("questionData " + questionData);
                console.log("=================");
                return response.success(questionData);
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
      }
    }, function(error) {
      return response.error(error);
    });
  });


  /*
  Parse.Cloud.define "getPreviousQuestion", (request, response) ->
   *	if !request.user
   *		response.error('Must be logged in.')
   *
   *	else
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
  								if _.isUndefined(questionObj.get('previousQuestion'))
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
  						if _.isUndefined(questionObj.get('previousQuestion'))# and  not questionObj.get 'isChild'
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
   */

  Parse.Cloud.define("deleteDependentQuestions", function(request, response) {
    var questionId, responseId, responseQuery;
    responseId = request.params.responseId;
    questionId = request.params.questionId;
    responseQuery = new Parse.Query('Response');
    return responseQuery.get(responseId).then(function(responseObj) {
      var questionQuery;
      questionQuery = new Parse.Query('Questions');
      questionQuery.include('previousQuestion');
      return questionQuery.get(questionId).then(function(questionsObj) {
        return deleteDependentQuestions(responseObj, questionsObj).then(function(questionsObj) {
          return response.success(questionsObj);
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

  deleteDependentQuestions = function(responseObj, questionsObj) {
    var promise;
    promise = new Parse.Promise();
    promise.resolve(questionsObj);
    return promise;
  };

  getPreviousQuestion = function(questionObj, responseObj) {
    var answer, answeredQuestions, i, j, len, promise, questionsQuery, result, result1;
    promise = new Parse.Promise();
    answeredQuestions = responseObj.get('answeredQuestions');
    result1 = "";
    result = "";
    for (i = j = 0, len = answeredQuestions.length; j < len; i = ++j) {
      answer = answeredQuestions[i];
      if ((questionObj.id === answer) && (i === 0)) {
        result = "previousNotDefined";
      } else if (questionObj.id === answer) {
        result = result1;
      }
      result1 = answer;
    }
    if (result === "" && (answeredQuestions.length === 0)) {
      promise.reject("invalidRequest");
    } else if (result === 'previousNotDefined') {
      promise.reject("previousNotDefined");
    } else {
      if (result === "") {
        result = answeredQuestions[answeredQuestions.length - 1];
      }
      questionsQuery = new Parse.Query('Questions');
      questionsQuery.include('questionnaire');
      questionsQuery.include('previousQuestion');
      questionsQuery.get(result).then(function(previousQuestion) {
        return promise.resolve(previousQuestion);
      }, function(error) {
        return promise.reject(error);
      });
    }
    return promise;
  };

  getLastQuestion = function(responseObj) {
    var answeredQuestions, promise, questionsQuery;
    promise = new Parse.Promise();
    answeredQuestions = responseObj.get('answeredQuestions');
    if (answeredQuestions.length === 0) {
      promise.resolve({});
    } else {
      questionsQuery = new Parse.Query('Questions');
      questionsQuery.get(answeredQuestions[answeredQuestions.length - 1]).then(function(lastQuestion) {
        return promise.resolve(lastQuestion);
      }, function(error) {
        return promise.reject(error);
      });
    }
    return promise;
  };

  Parse.Cloud.define('getSummary', function(request, response) {
    var responseId, responseQuery;
    responseId = request.params.responseId;
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo("objectId", responseId);
    return responseQuery.first().then(function(responseObj) {
      return getSummary(responseObj).then(function(answerObjects) {
        var result;
        result = {};
        result['answerObjects'] = answerObjects;
        result['submissionDate'] = responseObj.updatedAt;
        result['sequenceNumber'] = responseObj.get('sequenceNumber');
        return response.success(result);
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
    answerQuery.descending('createdAt');
    answerQuery.equalTo("response", responseObj);
    answerQuery.find().then(function(answerObjects) {
      answerObjects = getSequence(answerObjects, responseObj.get('answeredQuestions'));
      return promise.resolve(getAnswers(answerObjects));
    }, function(error) {
      return promise.error(error);
    });
    return promise;
  };

  getSequence = function(answerObjects, answeredQuestions) {
    var answer, answerObj, answerObjects1, j, k, len, len1;
    answerObjects1 = [];
    for (j = 0, len = answeredQuestions.length; j < len; j++) {
      answer = answeredQuestions[j];
      for (k = 0, len1 = answerObjects.length; k < len1; k++) {
        answerObj = answerObjects[k];
        if (answerObj.get('question').id === answer) {
          answerObjects1.push(answerObj);
        }
      }
    }
    return answerObjects1;
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

  Parse.Cloud.define("dashboard2", function(request, response) {
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
            return getCompletedObjects(scheduleObj, patientId).then(function(completedObj) {
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

  getStartObject = function(scheduleObj, patientId) {
    var promise, responseQuery, responseQuery1, responseQuery2, startObj, timeObj;
    startObj = {};
    promise = new Parse.Promise();
    timeObj = getValidPeriod(scheduleObj);
    if (isValidTime(timeObj)) {
      responseQuery1 = new Parse.Query('Response');
      responseQuery1.equalTo('status', 'started');
      responseQuery2 = new Parse.Query('Response');
      responseQuery2.equalTo('status', 'completed');
      responseQuery = Parse.Query.or(responseQuery1, responseQuery2);
      responseQuery.equalTo('patient', patientId);
      responseQuery.greaterThanOrEqualTo('createdAt', timeObj['lowerLimit']);
      responseQuery.lessThanOrEqualTo('createdAt', timeObj['upperLimit']);
      responseQuery.first().then(function(responseObj) {
        if (!_.isUndefined(responseObj)) {
          startObj['status'] = "not_start";
          startObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
        } else {
          startObj['status'] = "start";
          startObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
        }
        return promise.resolve(startObj);
      }, function(error) {
        return promise.error(error);
      });
    } else {
      startObj['status'] = "not_start";
      startObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
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
      upcomingObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
      promise.resolve(upcomingObj);
    } else {
      upcomingObj['status'] = "not_upcoming";
      upcomingObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
      promise.resolve(upcomingObj);
    }
    return promise;
  };

  getCompletedObjects = function(scheduleObj, patientId) {
    var completedObj, promise, responseQuery;
    completedObj = {};
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('patient', patientId);
    responseQuery.equalTo('status', 'completed');
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
      completedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
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
      responseQuery.equalTo('status', 'started');
      responseQuery.greaterThanOrEqualTo('createdAt', timeObj['lowerLimit']);
      responseQuery.lessThanOrEqualTo('createdAt', timeObj['upperLimit']);
      responseQuery.first().then(function(responseObj) {
        if (!_.isUndefined(responseObj)) {
          resumeObj['status'] = "resume";
          resumeObj['responseId'] = responseObj.id;
          resumeObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
        } else {
          resumeObj['status'] = "not_resume";
          resumeObj['responseId'] = "";
          resumeObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
        }
        return promise.resolve(resumeObj);
      }, function(error) {
        return promise.error(error);
      });
    } else {
      resumeObj['status'] = "not_resume";
      resumeObj['responseId'] = "";
      resumeObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
      promise.resolve(resumeObj);
    }
    return promise;
  };

  getMissedObjects = function(scheduleObj, patientId) {
    var missedObj, promise, responseQuery;
    missedObj = {};
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('patient', patientId);
    responseQuery.equalTo('status', 'missed');
    responseQuery.descending('createdAt');
    responseQuery.first().then(function(responseObj) {
      if (!_.isUndefined(responseObj) && responseObj.get('status') === 'missed') {
        missedObj['status'] = "missed";
        missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
        return promise.resolve(missedObj);
      } else {
        missedObj['status'] = "not_missed";
        missedObj['occurrenceDate'] = scheduleObj.get('nextOccurrence');
        return promise.resolve(missedObj);
      }
    }, function(error) {
      return promise.error(error);
    });
    return promise;
  };

  Parse.Cloud.define("dashboard1", function(request, response) {
    var patientId, results, scheduleQuery;
    patientId = request.params.patientId;
    results = [];
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.equalTo('patient', patientId);
    scheduleQuery.include('questionnaire');
    return scheduleQuery.first().then(function(scheduleObj) {
      var responseQuery;
      responseQuery = new Parse.Query('Response');
      responseQuery.equalTo('patient', patientId);
      responseQuery.descending('createdAt');
      return responseQuery.find().then(function(responseObjs) {
        var j, len, responseObj, result, status, timeObj, upcoming_due;
        timeObj = getValidPeriod(scheduleObj);
        status = "";
        if (isValidTime(timeObj)) {
          status = "Start";
        } else if (isValidUpcomingTime(timeObj)) {
          status = "Upcoming";
        }
        upcoming_due = {
          date: scheduleObj.get('nextOccurrence'),
          status: status
        };
        results.push(upcoming_due);
        for (j = 0, len = responseObjs.length; j < len; j++) {
          responseObj = responseObjs[j];
          result = {};
          result['status'] = responseObj.get('status');
          result['date'] = responseObj.createdAt;
          result['responseId'] = responseObj.id;
          results.push(result);
        }
        return response.success(results);
      }, function(error) {
        return response.error(error);
      });
    }, function(error) {
      return response.error(error);
    });
  });

  Parse.Cloud.define("updateMissedObjects", function(request, response) {
    var patientId, scheduleQuery;
    patientId = request.params.patientId;
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.equalTo('patient', patientId);
    scheduleQuery.include('questionnaire');
    return scheduleQuery.first().then(function(scheduleObj) {
      return updateMissedObjects(scheduleObj, patientId).then(function() {
        return response.success(scheduleObj);
      }, function(error) {
        return promise.error(error);
      });
    }, function(error) {
      return promise.error(error);
    });
  });

  Parse.Cloud.define("dashboard", function(request, response) {
    var patientId, results, scheduleQuery;
    console.log("---------------------------");
    console.log(request);
    console.log("-------------------------------");
    results = [];
    patientId = request.params.patientId;
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.include('questionnaire');
    scheduleQuery.equalTo('patient', patientId);
    return scheduleQuery.first().then(function(scheduleObj) {
      return updateMissedObjects(scheduleObj, patientId).then(function() {
        var responseQuery;
        responseQuery = new Parse.Query('Response');
        responseQuery.equalTo('patient', patientId);
        responseQuery.descending('occurrenceDate');
        return responseQuery.find().then(function(responseObjs) {
          return getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence')).then(function(timeObj) {
            var j, len, responseObj, result, status, upcoming_due;
            status = "";
            if (isValidTime(timeObj)) {
              status = "due";
            } else if (isValidUpcomingTime(timeObj)) {
              status = "upcoming";
            }
            upcoming_due = {
              occurrenceDate: scheduleObj.get('nextOccurrence'),
              status: status
            };
            results.push(upcoming_due);
            for (j = 0, len = responseObjs.length; j < len; j++) {
              responseObj = responseObjs[j];
              result = {};
              result['status'] = responseObj.get('status');
              result['occurrenceDate'] = responseObj.get('occurrenceDate');
              result['occurrenceId'] = responseObj.id;
              results.push(result);
            }
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
  });

  updateMissedObjects = function(scheduleObj, patientId) {
    var promise, responseQuery;
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('patient', patientId);
    responseQuery.equalTo('status', 'started');
    responseQuery.include('questionnaire');
    responseQuery.first().then(function(responseObj) {
      if (!_.isUndefined(responseObj)) {
        return getValidTimeFrame(responseObj.get('questionnaire'), responseObj.get('occurrenceDate')).then(function(timeObj) {
          if (isValidMissedTime(timeObj)) {
            responseObj.set('status', 'missed');
            return responseObj.save().then(function(responseObj) {
              return promise.resolve();
            }, function(error) {
              return promise.error(error);
            });
          } else {
            return promise.resolve();
          }
        }, function(error) {
          return promise.error(error);
        });
      } else {
        return getValidTimeFrame(scheduleObj.get('questionnaire'), scheduleObj.get('nextOccurrence')).then(function(timeObj) {
          if (isValidMissedTime(timeObj)) {
            return createResponse(scheduleObj.get('questionnaire').id, patientId, scheduleObj).then(function(responseObj) {
              responseObj.set('occurrenceDate', scheduleObj.get('nextOccurrence'));
              responseObj.set('status', 'missed');
              return responseObj.save().then(function(responseObj) {
                var scheduleQuery;
                scheduleQuery = new Parse.Query('Schedule');
                scheduleQuery.doesNotExist('patient');
                scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'));
                return scheduleQuery.first().then(function(scheduleQuestionnaireObj) {
                  var newNextOccurrence;
                  newNextOccurrence = moment(scheduleObj.get('nextOccurrence')).add(scheduleQuestionnaireObj.get('frequency'), 's').format();
                  newNextOccurrence = new Date(newNextOccurrence);
                  scheduleObj.set('nextOccurrence', newNextOccurrence);
                  return scheduleObj.save().then(function(scheduleObj) {
                    return promise.resolve();
                  }, function(error) {
                    return promise.error(error);
                  });
                }, function(error) {
                  return promise.error(error);
                });
              }, function(error) {
                return promise.error(error);
              });
            }, function(error) {
              return promise.error(error);
            });
          } else {
            return promise.resolve();
          }
        }, function(error) {
          return promise.error(error);
        });
      }
    }, function(error) {
      return promise.error(error);
    });
    return promise;
  };

  createResponse = function(questionnaireId, patientId, scheduleObj) {
    var promise, questionnaireQuery;
    promise = new Parse.Promise();
    questionnaireQuery = new Parse.Query("Questionnaire");
    questionnaireQuery.get(questionnaireId).then(function(questionnaireObj) {
      var responseQuery;
      responseQuery = new Parse.Query('Response');
      responseQuery.equalTo('questionnaire', questionnaireObj);
      responseQuery.equalTo('patient', patientId);
      responseQuery.descending('createdAt');
      responseQuery.notEqualTo('status', 'base_line');
      return responseQuery.first().then(function(responseObj_prev) {
        var responseObj;
        responseObj = new Parse.Object("Response");
        responseObj.set('patient', patientId);
        responseObj.set('hospital', questionnaireObj.get('hospital'));
        responseObj.set('project', questionnaireObj.get('project'));
        responseObj.set('questionnaire', questionnaireObj);
        responseObj.set('answeredQuestions', []);
        responseObj.set('schedule', scheduleObj);
        responseObj.set('baseLineFlagStatus', 'open');
        responseObj.set('previousFlagStatus', 'open');
        if (_.isUndefined(responseObj_prev)) {
          responseObj.set('sequenceNumber', 1.);
        } else {
          responseObj.set('sequenceNumber', responseObj_prev.get('sequenceNumber') + 1);
        }
        return responseObj.save().then(function(responseObj) {
          return promise.resolve(responseObj);
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  isValidUpcomingTime = function(timeObj) {
    var currentDateTime;
    currentDateTime = moment().format();
    if (moment(timeObj['lowerLimit']).isAfter(currentDateTime, 'second')) {
      return true;
    } else {
      return false;
    }
  };

  isValidMissedTime = function(timeObj) {
    var currentDateTime;
    currentDateTime = moment().format();
    console.log('*-----------------*');
    console.log("upperLimit");
    console.log(timeObj['upperLimit']);
    console.log("currentDateTime");
    console.log(currentDateTime);
    console.log('*-----------------*');
    if (moment(timeObj['upperLimit']).isBefore(currentDateTime, 'second')) {
      return true;
    } else {
      return false;
    }
  };

  getValidTimeFrame = function(questionnaireObj, occurrenceDate) {
    var promise, scheduleQuery;
    promise = new Parse.Promise();
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.doesNotExist('patient');
    scheduleQuery.equalTo('questionnaire', questionnaireObj);
    scheduleQuery.first().then(function(questionnaireScheduleObj) {
      var frequency, gracePeriod, lowerLimit, timeObj, upperLimit;
      gracePeriod = questionnaireObj.get('gracePeriod');
      frequency = questionnaireScheduleObj.get('frequency');
      upperLimit = moment(occurrenceDate).add(frequency, 's').format();
      upperLimit = moment(upperLimit).subtract(gracePeriod, 's').format();
      upperLimit = moment(upperLimit).subtract(60, 's').format();
      lowerLimit = moment(occurrenceDate).subtract(gracePeriod, 's').format();
      timeObj = {};
      timeObj['upperLimit'] = upperLimit;
      timeObj['lowerLimit'] = lowerLimit;
      return promise.resolve(timeObj);
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getValidPeriod = function(scheduleObj) {
    var promise, scheduleQuery;
    promise = new Parse.Promise();
    scheduleQuery = new Parse.Query('Schedule');
    scheduleQuery.doesNotExist('patient');
    scheduleQuery.equalTo('questionnaire', scheduleObj.get('questionnaire'));
    scheduleQuery.first().then(function(questionnaireScheduleObj) {
      var frequency, gracePeriod, lowerLimit, nextOccurrence, timeObj, upperLimit;
      nextOccurrence = scheduleObj.get('nextOccurrence');
      gracePeriod = scheduleObj.get('questionnaire').get('gracePeriod');
      frequency = questionnaireScheduleObj.get('frequency');
      upperLimit = moment(nextOccurrence).add(frequency, 's').format();
      upperLimit = moment(upperLimit).subtract(gracePeriod, 's').format();
      upperLimit = moment(upperLimit).subtract(60, 's').format();
      lowerLimit = moment(nextOccurrence).subtract(gracePeriod, 's').format();
      timeObj = {};
      timeObj['lowerLimit'] = lowerLimit;
      timeObj['upperLimit'] = upperLimit;
      return promise.resolve(timeObj);
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  isValidTime = function(timeObj) {
    var currentDateTime;
    currentDateTime = moment().format();
    if ((moment(timeObj['lowerLimit']).isSameOrBefore(currentDateTime, 'second')) && (moment(timeObj['upperLimit']).isSameOrAfter(currentDateTime, 'second'))) {
      return true;
    } else {
      return false;
    }
  };

  saveAnswer1 = function(responseObj, questionObj, options, value) {
    var promise, promiseArr, responseObject;
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
    getCurrentAnswer(questionObj, responseObj).then(function(hasAnswer) {
      var answerQuery, isEditable;
      isEditable = questionObj.get('questionnaire').get('editable');
      if (!isEditable && !_.isEmpty(hasAnswer)) {
        return promise.resolve("already_answered");
      } else if (isEditable && !_.isEmpty(hasAnswer)) {
        answerQuery = new Parse.Query('Answer');
        answerQuery.equalTo('response', responseObj);
        answerQuery.equalTo('question', questionObj);
        return answerQuery.find().then(function(answers) {
          var promiseDelete;
          promiseDelete = Parse.Promise.as();
          _.each(answers, function(answer) {
            return promiseDelete = promiseDelete.then(function() {
              return answer.destroy();
            }, function(error) {
              return promise.error(error);
            });
          });
          return promiseDelete;
        }, function(error) {
          return promise.error(error);
        }).then(function() {
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
        }, function(error) {
          return promise.error(error);
        });
      } else {
        console.log("++++++++++++++++++++++++++++++++++++++++++++++++++");
        console.log(isEditable);
        console.log(hasAnswer);
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
      }
    }, function(error) {
      return promise.error(error);
    });
    return promise;
  };

  Parse.Cloud.define("saveAnswer", function(request, response) {
    var options, questionId, responseId, responseQuery, value;
    responseId = request.params.responseId;
    questionId = request.params.questionId;
    options = request.params.options;
    value = request.params.value;
    responseQuery = new Parse.Query('Response');
    responseQuery.include('questionnaire');
    return responseQuery.get(responseId).then(function(responseObj) {
      var questionsQuery;
      questionsQuery = new Parse.Query('Questions');
      questionsQuery.include('questionnaire');
      return questionsQuery.get(questionId).then(function(questionsObj) {
        return saveAnswer(responseObj, questionsObj, options, value).then(function(answerObjs) {
          return response.success(answerObjs);
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

  saveAnswer = function(responseObj, questionsObj, options, value) {
    var promise;
    promise = new Parse.Promise();
    if (questionsObj.get('type') === 'single-choice') {
      saveSingleChoice(responseObj, questionsObj, options).then(function(answerObj) {
        var answeredQuestions, ref;
        answeredQuestions = responseObj.get('answeredQuestions');
        if (ref = questionsObj.id, indexOf.call(answeredQuestions, ref) < 0) {
          answeredQuestions.push(questionsObj.id);
        }
        responseObj.set('answeredQuestions', answeredQuestions);
        return responseObj.save().then(function(responseObj) {
          return promise.resolve();
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    } else if (questionsObj.get('type') === 'multi-choice') {
      saveMultiChoice(responseObj, questionsObj, options).then(function(answerObjs) {
        var answeredQuestions, ref;
        answeredQuestions = responseObj.get('answeredQuestions');
        if (ref = questionsObj.id, indexOf.call(answeredQuestions, ref) < 0) {
          answeredQuestions.push(questionsObj.id);
        }
        responseObj.set('answeredQuestions', answeredQuestions);
        return responseObj.save().then(function(responseObj) {
          return promise.resolve();
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    } else if (questionsObj.get('type') === 'input') {
      saveInput(responseObj, questionsObj, options, value).then(function(answerObj) {
        var answeredQuestions, ref;
        answeredQuestions = responseObj.get('answeredQuestions');
        if (ref = questionsObj.id, indexOf.call(answeredQuestions, ref) < 0) {
          answeredQuestions.push(questionsObj.id);
        }
        responseObj.set('answeredQuestions', answeredQuestions);
        return responseObj.save().then(function(responseObj) {
          return promise.resolve();
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    } else {
      saveDescriptive(responseObj, questionsObj, value).then(function(answerObj) {
        var answeredQuestions, ref;
        answeredQuestions = responseObj.get('answeredQuestions');
        if (ref = questionsObj.id, indexOf.call(answeredQuestions, ref) < 0) {
          answeredQuestions.push(questionsObj.id);
        }
        responseObj.set('answeredQuestions', answeredQuestions);
        return responseObj.save().then(function(responseObj) {
          return promise.resolve();
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    }
    return promise;
  };

  saveMultiChoice = function(responseObj, questionsObj, options) {
    var deleteAnswers, promise, promiseArr;
    promiseArr = [];
    promise = new Parse.Promise();
    getAnswers = function() {
      var promise1;
      promise1 = Parse.Promise.as();
      _.each(options, function(optionsId) {
        return promise1 = promise1.then(function() {
          var optionsQuery;
          optionsQuery = new Parse.Query('Options');
          return optionsQuery.get(optionsId).then(function(optionsObj) {
            var answer;
            answer = new Parse.Object('Answer');
            answer.set("response", responseObj);
            answer.set("patient", responseObj.get('patient'));
            answer.set("question", questionsObj);
            answer.set("option", optionsObj);
            answer.set("score", optionsObj.get('score'));
            answer.set('project', responseObj.get('project'));
            answer.set('occurrenceDate', responseObj.get('occurrenceDate'));
            return answer.save();
          }, function(error) {
            return promise.reject(error);
          });
        }, function(error) {
          return promise.reject(error);
        });
      });
      return promise1;
    };
    deleteAnswers = function(answers) {
      var promise1;
      promise1 = Parse.Promise.as();
      _.each(answers, function(answerObj) {
        return promise1 = promise1.then(function() {
          return answerObj.destroy();
        }, function(error) {
          return promise.reject(error);
        });
      });
      return promise1;
    };
    getCurrentAnswer(questionsObj, responseObj).then(function(hasAnswer) {
      var questionnaireQuery;
      questionnaireQuery = new Parse.Query('Questionnaire');
      return questionnaireQuery.get(responseObj.get('questionnaire').id).then(function(questionnaire) {
        var answerQuery, isEditable;
        isEditable = questionnaire.get('editable');
        if (!isEditable && !_.isEmpty(hasAnswer)) {
          return promise.resolve("notEditable");
        } else if (isEditable && !_.isEmpty(hasAnswer)) {
          answerQuery = new Parse.Query('Answer');
          answerQuery.equalTo('question', questionsObj);
          answerQuery.equalTo('response', responseObj);
          return answerQuery.find().then(function(answers) {
            return deleteAnswers(answers).then(function() {
              return getAnswers().then(function() {
                return promise.resolve("saved");
              }, function(error) {
                return promise.reject(error);
              });
            }, function(error) {
              return promise.reject(error);
            });
          }, function(error) {
            return promise.reject(error);
          });
        } else {
          return getAnswers().then(function() {
            return promise.resolve("saved");
          }, function(error) {
            return promise.reject("someError");
          });
        }
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getFlag = function(value) {
    var flag;
    flag = "";
    if (value <= -2) {
      flag = "red";
    } else if (value === -1) {
      flag = "amber";
    } else if (value === 0) {
      flag = "no_colour";
    } else {
      flag = "green";
    }
    return flag;
  };

  Parse.Cloud.define("submitQuestionnaire", function(request, response) {
    var responseId, responseQuery;
    responseId = request.params.responseId;
    responseQuery = new Parse.Query("Response");
    responseQuery.include('questionnaire');
    return responseQuery.get(responseId).then(function(responseObj) {
      return getBaseLineScores(responseObj).then(function(BaseLine) {
        return getPreviousScores(responseObj).then(function(previous) {
          var occurrenceDate, questionnaireObj, status;
          questionnaireObj = responseObj.get("questionnaire");
          occurrenceDate = responseObj.get("occurrenceDate");
          if (isLateSubmission(questionnaireObj, occurrenceDate)) {
            status = "late";
          } else {
            status = "completed";
          }
          responseObj.set("comparedToBaseLine", BaseLine['comparedToBaseLine']);
          responseObj.set("baseLineTotalRedFlags", BaseLine['baseLineTotalRedFlags']);
          responseObj.set("baseLineTotalAmberFlags", BaseLine['baseLineTotalAmberFlags']);
          responseObj.set("baseLineTotalGreenFlags", BaseLine['baseLineTotalGreenFlags']);
          responseObj.set("comparedToPrevious", previous['comparedToPrevious']);
          responseObj.set("previousTotalRedFlags", previous['previousTotalRedFlags']);
          responseObj.set("previousTotalAmberFlags", previous['previousTotalAmberFlags']);
          responseObj.set("previousTotalGreenFlags", previous['previousTotalGreenFlags']);
          responseObj.set("baseLineFlag", BaseLine['baseLineFlag']);
          responseObj.set("previousFlag", previous['previousFlag']);
          responseObj.set("status", status);
          responseObj.set("totalScore", BaseLine['totalScore']);
          responseObj.set("baseLine", BaseLine['baseLine']);
          responseObj.set("baseLineScore", BaseLine['baseLineScore']);
          responseObj.set("previousScore", previous['previousScore']);
          if (previous['previousSubmission'] !== '') {
            responseObj.set("previousSubmission", previous['previousSubmission']);
          }
          return responseObj.save().then(function(responseObj) {
            return response.success("submitted_successfully");
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

  isLateSubmission = function(questionnaireObj, occurrenceDate) {
    var currentDateTime, graceDate, gracePeriod, result;
    gracePeriod = questionnaireObj.get('gracePeriod');
    currentDateTime = moment().format();
    graceDate = moment(occurrenceDate).add(gracePeriod, 's').format();
    if (moment(currentDateTime).isAfter(graceDate, 'second')) {
      console.log("LATE SUBMISSION");
      result = true;
    } else {
      console.log("COMPLETED");
      result = false;
    }
    return result;
  };

  Parse.Cloud.define("isLateSubmission", function(request, response) {
    var currentDateTime, gracePeriod, occurrenceDate, resumeObj;
    occurrenceDate = request.params.occurrenceDate;
    currentDateTime = moment().format();
    gracePeriod = 1000;
    occurrenceDate = moment(occurrenceDate).add(gracePeriod, 's').format();
    resumeObj = {};
    resumeObj['occurrenceDate'] = occurrenceDate;
    resumeObj['currentDateTime'] = currentDateTime;
    if (moment(currentDateTime).isAfter(occurrenceDate, 'second')) {
      console.log("LATE SUBMISSION");
      resumeObj['status'] = "LATE SUBMISSION";
    } else {
      console.log("COMPLETED");
      resumeObj['status'] = "COMPLETED";
    }
    return response.success(resumeObj);
  });

  getPreviousScores = function(responseObj) {
    var promise, responseQuery;
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('patient', responseObj.get('patient'));
    responseQuery.equalTo('questionnaire', responseObj.get('questionnaire'));
    responseQuery.containedIn('status', ['completed', 'late']);
    responseQuery.descending('occurrenceDate');
    responseQuery.first().then(function(previousResponseObj) {
      var answerQuery, previous;
      if (!_.isEmpty(previousResponseObj)) {
        answerQuery = new Parse.Query('Answer');
        answerQuery.include('question');
        answerQuery.equalTo('response', previousResponseObj);
        return answerQuery.find().then(function(previousAnswers) {
          answerQuery = new Parse.Query('Answer');
          answerQuery.include('question');
          answerQuery.equalTo('response', responseObj);
          return answerQuery.find().then(function(answers) {
            var answer, j, k, len, len1, previous, totalAmberFlags, totalAnswerScore, totalGreenFlags, totalPreviousScore, totalRedFlags;
            totalPreviousScore = 0;
            totalAnswerScore = 0;
            totalRedFlags = 0;
            totalAmberFlags = 0;
            totalGreenFlags = 0;
            for (j = 0, len = answers.length; j < len; j++) {
              answer = answers[j];
              if (answer.get('previousFlag') === 'red') {
                totalRedFlags = parseInt(totalRedFlags) + 1;
              } else if (answer.get('previousFlag') === 'amber') {
                totalAmberFlags = parseInt(totalAmberFlags) + 1;
              } else if (answer.get('previousFlag') === 'green') {
                totalGreenFlags = parseInt(totalGreenFlags) + 1;
              }
              if (answer.get('question').get('type') === 'single-choice') {
                totalAnswerScore += answer.get('score');
              }
            }
            for (k = 0, len1 = previousAnswers.length; k < len1; k++) {
              answer = previousAnswers[k];
              if (answer.get('question').get('type') === 'single-choice') {
                totalPreviousScore += answer.get('score');
              }
            }
            previous = {};
            previous['previousSubmission'] = previousResponseObj;
            previous['comparedToPrevious'] = totalPreviousScore - totalAnswerScore;
            previous['previousFlag'] = getFlag(totalPreviousScore - totalAnswerScore);
            previous['previousTotalRedFlags'] = totalRedFlags;
            previous['previousTotalAmberFlags'] = totalAmberFlags;
            previous['previousTotalGreenFlags'] = totalGreenFlags;
            previous['previousScore'] = totalPreviousScore;
            return promise.resolve(previous);
          }, function(error) {
            return promise.reject(error);
          });
        }, function(error) {
          return promise.reject(error);
        });
      } else {
        previous = {};
        previous['previousSubmission'] = '';
        previous['comparedToPrevious'] = 0;
        previous['previousFlag'] = '';
        previous['previousTotalRedFlags'] = 0;
        previous['previousTotalAmberFlags'] = 0;
        previous['previousTotalGreenFlags'] = 0;
        return promise.resolve(previous);
      }
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getBaseLineScores = function(responseObj) {
    var promise, responseQuery;
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('patient', responseObj.get('patient'));
    responseQuery.equalTo('questionnaire', responseObj.get('questionnaire'));
    responseQuery.equalTo('status', 'base_line');
    responseQuery.descending('createdAt');
    responseQuery.first().then(function(responseBaseLine) {
      var answerQuery;
      answerQuery = new Parse.Query('Answer');
      answerQuery.equalTo('response', responseBaseLine);
      answerQuery.include('question');
      return answerQuery.find().then(function(answersBaseLine) {
        answerQuery = new Parse.Query('Answer');
        answerQuery.include('question');
        answerQuery.equalTo('response', responseObj);
        return answerQuery.find().then(function(answers) {
          var BaseLine, answer, j, k, len, len1, totalAmberFlags, totalAnswerScore, totalBaseLineScore, totalGreenFlags, totalRedFlags;
          totalBaseLineScore = 0;
          totalAnswerScore = 0;
          totalRedFlags = 0;
          totalAmberFlags = 0;
          totalGreenFlags = 0;
          for (j = 0, len = answers.length; j < len; j++) {
            answer = answers[j];
            if (answer.get('baseLineFlag') === 'red') {
              totalRedFlags = parseInt(totalRedFlags) + 1;
            } else if (answer.get('baseLineFlag') === 'amber') {
              totalAmberFlags = parseInt(totalAmberFlags) + 1;
            } else if (answer.get('baseLineFlag') === 'green') {
              totalGreenFlags = parseInt(totalGreenFlags) + 1;
            }
            if (answer.get('question').get('type') === 'single-choice') {
              totalAnswerScore += answer.get('score');
            }
          }
          for (k = 0, len1 = answersBaseLine.length; k < len1; k++) {
            answer = answersBaseLine[k];
            if (answer.get('question').get('type') === 'single-choice') {
              totalBaseLineScore += answer.get('score');
            }
          }
          BaseLine = {};
          BaseLine['baseLine'] = responseBaseLine;
          BaseLine['totalScore'] = totalAnswerScore;
          BaseLine['comparedToBaseLine'] = totalBaseLineScore - totalAnswerScore;
          BaseLine['baseLineFlag'] = getFlag(totalBaseLineScore - totalAnswerScore);
          BaseLine['baseLineTotalRedFlags'] = totalRedFlags;
          BaseLine['baseLineTotalAmberFlags'] = totalAmberFlags;
          BaseLine['baseLineTotalGreenFlags'] = totalGreenFlags;
          BaseLine['baseLineScore'] = totalBaseLineScore;
          return promise.resolve(BaseLine);
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getBaseLineValues = function(responseObj, questionsObj, optionsObj) {
    var promise, responseQuery;
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.equalTo('patient', responseObj.get('patient'));
    responseQuery.equalTo('questionnaire', responseObj.get('questionnaire'));
    responseQuery.descending('updatedAt');
    responseQuery.equalTo('status', 'base_line');
    responseQuery.first().then(function(responseBaseLine) {
      var answerQuery;
      console.log("responseBaseLine");
      console.log(responseBaseLine);
      answerQuery = new Parse.Query('Answer');
      answerQuery.equalTo('response', responseBaseLine);
      answerQuery.equalTo('question', questionsObj);
      return answerQuery.first().then(function(BaseLineAnswer) {
        var BaseLine, BaseLineValue;
        console.log("BaseLineAnswer");
        console.log(BaseLineAnswer);
        BaseLineValue = BaseLineAnswer.get('score');
        BaseLineValue = BaseLineValue - optionsObj.get('score');
        BaseLine = {};
        BaseLine['comparedToBaseLine'] = BaseLineValue;
        BaseLine['baseLineFlag'] = getFlag(BaseLineValue);
        return promise.resolve(BaseLine);
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getPreviousQuestionnaireAnswer = function(questionObject, responseObj, patientId) {
    var answerQuery, promise;
    promise = new Parse.Promise();
    answerQuery = new Parse.Query('Answer');
    answerQuery.equalTo("question", questionObject);
    answerQuery.include('response');
    answerQuery.equalTo("patient", patientId);
    answerQuery.notEqualTo("response", responseObj);
    answerQuery.descending('updatedAt');
    answerQuery.find().then(function(answerObjects) {
      var answerObj, first, optionIds, result;
      result = {};
      answerObjects = (function() {
        var j, len, results1;
        results1 = [];
        for (j = 0, len = answerObjects.length; j < len; j++) {
          answerObj = answerObjects[j];
          if (answerObj.get('response').get('status') === 'completed' || answerObj.get('response').get('status') === 'late') {
            results1.push(answerObj);
          }
        }
        return results1;
      })();
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
          "date": answerObjects[0].updatedAt,
          "answerId": answerObjects[0].id
        };
      }
      return promise.resolve(result);
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  getPreviousValues = function(responseObj, questionsObj, optionsObj) {
    var promise;
    promise = new Parse.Promise();
    getPreviousQuestionnaireAnswer(questionsObj, responseObj, responseObj.get('patient')).then(function(previousQuestion) {
      var answerQuery;
      if (!_.isEmpty(previousQuestion)) {
        answerQuery = new Parse.Query('Answer');
        return answerQuery.get(previousQuestion['answerId']).then(function(answerObj) {
          var previous, previousFlag, previousValue;
          previousValue = answerObj.get('score');
          previousValue = previousValue - optionsObj.get('score');
          previousFlag = "";
          if (previousValue <= -2) {
            previousFlag = "red";
          } else if (previousValue === -1) {
            previousFlag = "amber";
          } else if (previousValue === 0) {
            previousFlag = "no_colour";
          } else {
            previousFlag = "green";
          }
          previous = {};
          previous['comparedToPrevious'] = previousValue;
          previous['previousFlag'] = previousFlag;
          return promise.resolve(previous);
        }, function(error) {
          return promise.reject(error);
        });
      } else {
        return getBaseLineValues(responseObj, questionsObj, optionsObj).then(function(BaseLine) {
          var previous;
          previous = {};
          previous['comparedToPrevious'] = 0;
          previous['previousFlag'] = '';
          return promise.resolve(previous);
        }, function(error) {
          return promise.reject(error);
        });
      }
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  saveSingleChoice = function(responseObj, questionsObj, options) {
    var promise;
    promise = new Parse.Promise();
    console.log("---------------------");
    console.log("responseObj " + responseObj.id);
    console.log("questionsObj " + questionsObj.id);
    console.log("options " + options.length);
    console.log("---------------------");
    getCurrentAnswer(questionsObj, responseObj).then(function(hasAnswer) {
      var questionnaireQuery;
      questionnaireQuery = new Parse.Query('Questionnaire');
      return questionnaireQuery.get(responseObj.get('questionnaire').id).then(function(questionnaire) {
        var answer, answerQuery, isEditable;
        console.log("hasAnswer " + hasAnswer);
        isEditable = questionnaire.get('editable');
        console.log("isEditable " + isEditable);
        if (!isEditable && !_.isEmpty(hasAnswer)) {
          return promise.resolve("notEditable");
        } else if (isEditable && !_.isEmpty(hasAnswer)) {
          answerQuery = new Parse.Query('Answer');
          answerQuery.equalTo('response', responseObj);
          answerQuery.equalTo('question', questionsObj);
          return answerQuery.first().then(function(answer) {
            var optionsQuery;
            console.log("answer " + answer);
            if (!_.isEmpty(options)) {
              optionsQuery = new Parse.Query("Options");
              optionsQuery.equalTo('question', questionsObj);
              optionsQuery.equalTo('objectId', options[0]);
              return optionsQuery.first().then(function(optionsObj) {
                console.log("optionsObj " + optionsObj.id);
                return getBaseLineValues(responseObj, questionsObj, optionsObj).then(function(BaseLine) {
                  console.log("BaseLine " + BaseLine);
                  return getPreviousValues(responseObj, questionsObj, optionsObj).then(function(previous) {
                    console.log("previous " + previous);
                    answer.set("option", optionsObj);
                    answer.set("score", optionsObj.get('score'));
                    answer.set("comparedToBaseLine", BaseLine['comparedToBaseLine']);
                    answer.set("comparedToPrevious", previous['comparedToPrevious']);
                    answer.set("baseLineFlag", BaseLine['baseLineFlag']);
                    answer.set("previousFlag", previous['previousFlag']);
                    answer.set('occurrenceDate', responseObj.get('occurrenceDate'));
                    return answer.save().then(function(answer) {
                      console.log("answer " + answer);
                      return promise.resolve(answer);
                    }, function(error) {
                      return promise.reject(error);
                    });
                  }, function(error) {
                    return promise.reject(error);
                  });
                }, function(error) {
                  return promise.reject(error);
                });
              }, function(error) {
                return promise.reject(error);
              });
            } else {
              return promise.reject("noOptionSelected");
            }
          }, function(error) {
            return promise.reject(error);
          });
        } else {
          answer = new Parse.Object('Answer');
          answer.set("response", responseObj);
          answer.set("patient", responseObj.get('patient'));
          answer.set("question", questionsObj);
          answer.set('project', responseObj.get('project'));
          answer.set('occurrenceDate', responseObj.get('occurrenceDate'));
          return answer.save().then(function(answer) {
            var optionsQuery;
            if (!_.isEmpty(options)) {
              optionsQuery = new Parse.Query("Options");
              optionsQuery.equalTo('question', questionsObj);
              optionsQuery.equalTo('objectId', options[0]);
              return optionsQuery.first().then(function(optionsObj) {
                answer.set("option", optionsObj);
                answer.set("score", optionsObj.get('score'));
                return answer.save().then(function(answer) {
                  return getBaseLineValues(responseObj, questionsObj, optionsObj).then(function(BaseLine) {
                    return getPreviousValues(responseObj, questionsObj, optionsObj).then(function(previous) {
                      answer.set("option", optionsObj);
                      answer.set("score", optionsObj.get('score'));
                      answer.set("comparedToBaseLine", BaseLine['comparedToBaseLine']);
                      answer.set("comparedToPrevious", previous['comparedToPrevious']);
                      answer.set("baseLineFlag", BaseLine['baseLineFlag']);
                      answer.set("previousFlag", previous['previousFlag']);
                      answer.set("baseLineFlagStatus", 'open');
                      answer.set("previousFlagStatus", 'open');
                      return answer.save().then(function(answer) {
                        return promise.resolve(answer);
                      }, function(error) {
                        return promise.reject(error);
                      });
                    }, function(error) {
                      return promise.reject(error);
                    });
                  }, function(error) {
                    return promise.reject(error);
                  });
                }, function(error) {
                  return promise.reject(error);
                });
              }, function(error) {
                return promise.reject(error);
              });
            } else {
              return promise.reject("noOptionSelected");
            }
          }, function(error) {
            return promise.reject(error);
          });
        }
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  saveInput = function(responseObj, questionsObj, options, value) {
    var promise;
    promise = new Parse.Promise();
    getCurrentAnswer(questionsObj, responseObj).then(function(hasAnswer) {
      var questionnaireQuery;
      questionnaireQuery = new Parse.Query('Questionnaire');
      return questionnaireQuery.get(responseObj.get('questionnaire').id).then(function(questionnaire) {
        var answer, answerQuery, isEditable;
        isEditable = questionnaire.get('editable');
        if (!isEditable && !_.isEmpty(hasAnswer)) {
          return promise.resolve("notEditable");
        } else if (isEditable && !_.isEmpty(hasAnswer)) {
          answerQuery = new Parse.Query('Answer');
          answerQuery.equalTo('response', responseObj);
          answerQuery.equalTo('question', questionsObj);
          return answerQuery.first().then(function(answer) {
            var optionsQuery;
            if (!_.isEmpty(options)) {
              optionsQuery = new Parse.Query("Options");
              optionsQuery.equalTo('question', questionsObj);
              optionsQuery.equalTo('objectId', options[0]);
              return optionsQuery.first().then(function(optionsObj) {
                answer.set("option", optionsObj);
                answer.set("score", optionsObj.get('score'));
                answer.set("value", value);
                return answer.save().then(function(answer) {
                  return promise.resolve(answer);
                }, function(error) {
                  return promise.reject(error);
                });
              }, function(error) {
                return promise.reject(error);
              });
            } else {
              answer.set("value", value);
              return answer.save().then(function(answer) {
                return promise.resolve(answer);
              }, function(error) {
                return promise.reject(error);
              });
            }
          }, function(error) {
            return promise.reject(error);
          });
        } else {
          answer = new Parse.Object('Answer');
          answer.set("response", responseObj);
          answer.set("patient", responseObj.get('patient'));
          answer.set("question", questionsObj);
          answer.set("value", value);
          answer.set('project', responseObj.get('project'));
          answer.set('occurrenceDate', responseObj.get('occurrenceDate'));
          return answer.save().then(function(answer) {
            var optionsQuery;
            if (!_.isEmpty(options)) {
              optionsQuery = new Parse.Query("Options");
              optionsQuery.equalTo('question', questionsObj);
              optionsQuery.equalTo('objectId', options[0]);
              return optionsQuery.first().then(function(optionsObj) {
                answer.set("option", optionsObj);
                answer.set("score", optionsObj.get('score'));
                return answer.save().then(function(answer) {
                  return promise.resolve(answer);
                }, function(error) {
                  return promise.reject(error);
                });
              }, function(error) {
                return promise.reject(error);
              });
            } else {
              return promise.resolve(answer);
            }
          }, function(error) {
            return promise.reject(error);
          });
        }
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  saveDescriptive = function(responseObj, questionsObj, value) {
    var promise;
    promise = new Parse.Promise();
    getCurrentAnswer(questionsObj, responseObj).then(function(hasAnswer) {
      var questionnaireQuery;
      questionnaireQuery = new Parse.Query('Questionnaire');
      return questionnaireQuery.get(responseObj.get('questionnaire').id).then(function(questionnaire) {
        var answer, answerQuery, isEditable;
        isEditable = questionnaire.get('editable');
        if (!isEditable && !_.isEmpty(hasAnswer)) {
          return promise.resolve("notEditable");
        } else if (isEditable && !_.isEmpty(hasAnswer)) {
          answerQuery = new Parse.Query('Answer');
          answerQuery.equalTo('response', responseObj);
          answerQuery.equalTo('question', questionsObj);
          return answerQuery.first().then(function(answer) {
            answer.set("value", value);
            return answer.save().then(function(answer) {
              return promise.resolve(answer);
            }, function(error) {
              return promise.reject(error);
            });
          });
        } else {
          answer = new Parse.Object('Answer');
          answer.set("response", responseObj);
          answer.set("patient", responseObj.get('patient'));
          answer.set("question", questionsObj);
          answer.set("value", value);
          answer.set('project', responseObj.get('project'));
          answer.set('occurrenceDate', responseObj.get('occurrenceDate'));
          return answer.save().then(function(answer) {
            return promise.resolve(answer);
          }, function(error) {
            return promise.reject(error);
          });
        }
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define("baseLine", function(request, response) {
    var options, questionId, responseId, responseQuery, value;
    responseId = request.params.responseId;
    questionId = request.params.questionId;
    options = request.params.options;
    value = request.params.value;
    console.log("============================");
    console.log(new Date());
    console.log("===========================");
    responseQuery = new Parse.Query('Response');
    responseQuery.include('questionnaire');
    return responseQuery.get(responseId).then(function(responseObj) {
      var questionsQuery;
      questionsQuery = new Parse.Query('Questions');
      questionsQuery.include('questionnaire');
      return questionsQuery.get(questionId).then(function(questionsObj) {
        var answer;
        answer = new Parse.Object('Answer');
        answer.set("question", questionsObj);
        answer.set("patient", responseObj.get('patient'));
        answer.set("response", responseObj);
        answer.set("score", value);
        answer.set('project', responseObj.get('project'));
        return answer.save().then(function(answerObj) {
          return getNextQuestion(questionsObj, []).then(function(question) {
            return response.success(question);
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

  Parse.Cloud.define('deleteAllAnswers', function(request, response) {
    var responseId;
    responseId = request.params.responseId;
    return deleteAllAnswers(responseId).then(function(responseObj) {
      return response.success(responseObj);
    }, function(error) {
      return response.error(error);
    });
  });

  deleteAllAnswers = function(responseId) {
    var promise, responseQuery;
    promise = new Parse.Promise();
    responseQuery = new Parse.Query('Response');
    responseQuery.get(responseId).then(function(responseObj) {
      var answerQuery;
      answerQuery = new Parse.Query('Answer');
      answerQuery.equalTo('response', responseObj);
      return answerQuery.find().then(function(answerObjs) {
        var deleteAnswers;
        deleteAnswers = function() {
          var promise1;
          promise1 = Parse.Promise.as();
          _.each(answerObjs, function(answerObj) {
            return promise1 = promise1.then(function() {
              return answerObj.destroy({});
            }, function(error) {
              return promise1.reject(error);
            });
          });
          return promise1;
        };
        return deleteAnswers().then(function() {
          return responseObj.destroy({}).then(function() {
            return promise.resolve("deleted");
          }, function(error) {
            return promise.reject(error);
          });
        }, function(error) {
          return promise.reject(error);
        });
      }, function(error) {
        return promise.reject(error);
      });
    }, function(error) {
      return promise.reject(error);
    });
    return promise;
  };

  Parse.Cloud.define("listAllResponsesForProject", function(request, response) {
    var endDate, projectId, startDate;
    projectId = request.params.projectId;
    startDate = new Date(request.params.startDate);
    endDate = new Date(request.params.endDate);
    return listAllResponsesForProject(projectId, startDate, endDate).then(function(results) {
      console.log("___________");
      console.log("results.length " + results.length);
      console.log("___________");
      return response.success(results);
    }, function(error) {
      return response.error(error);
    });
  });

  listAllResponsesForProject = function(projectId, startDate, endDate) {
    var getAllProjectResponses, limit, page, promise, responseObjects;
    promise = new Parse.Promise();
    responseObjects = [];
    page = 0;
    limit = 100;
    getAllProjectResponses = function() {
      var responseQuery;
      responseQuery = new Parse.Query('Response');
      responseQuery.equalTo('project', projectId);
      responseQuery.descending('occurrenceDate');
      responseQuery.containedIn('status', ["completed", "missed", "base_line"]);
      responseQuery.greaterThanOrEqualTo('occurrenceDate', startDate);
      responseQuery.lessThanOrEqualTo('occurrenceDate', endDate);
      responseQuery.limit(limit);
      responseQuery.skip(page * limit);
      responseQuery.include('questionnaire');
      responseQuery.include('schedule');
      responseQuery.find().then(function(responseObjs) {
        var j, len, responseObj;
        console.log("___________");
        console.log("responseObjs.length " + responseObjs.length);
        console.log("___________");
        if (_.isEmpty(responseObjs)) {
          return promise.resolve(responseObjects);
        } else {
          for (j = 0, len = responseObjs.length; j < len; j++) {
            responseObj = responseObjs[j];
            responseObjects.push(responseObj);
          }
          page++;
          return getAllProjectResponses();
        }
      }, function(error) {
        return promise.reject(error);
      });
      return promise;
    };
    getAllProjectResponses();
    return promise;
  };

  Parse.Cloud.define("listAllAnswersForProject", function(request, response) {
    var endDate, projectId, startDate;
    projectId = request.params.projectId;
    startDate = new Date(request.params.startDate);
    endDate = new Date(request.params.endDate);
    return listAllAnswersForProject(projectId, startDate, endDate).then(function(results) {
      console.log("___________");
      console.log("results.length " + results.length);
      console.log("___________");
      return response.success(results);
    }, function(error) {
      return response.error(error);
    });
  });

  listAllAnswersForProject = function(projectId, startDate, endDate) {
    var answerObjects, getAllProjectAnswers, limit, page, promise;
    promise = new Parse.Promise();
    answerObjects = [];
    page = 0;
    limit = 100;
    getAllProjectAnswers = function() {
      var answerQuery;
      answerQuery = new Parse.Query('Answer');
      answerQuery.equalTo('project', projectId);
      answerQuery.descending('updatedAt');
      answerQuery.greaterThanOrEqualTo('occurrenceDate', startDate);
      answerQuery.lessThanOrEqualTo('occurrenceDate', endDate);
      answerQuery.limit(limit);
      answerQuery.skip(page * limit);
      answerQuery.include('question');
      answerQuery.include('response');
      answerQuery.include('option');
      answerQuery.find().then(function(answerObjs) {
        var answerObj, j, len;
        console.log("___________");
        console.log("answerObjs.length " + answerObjs.length);
        console.log("___________");
        if (_.isEmpty(answerObjs)) {
          return promise.resolve(answerObjects);
        } else {
          for (j = 0, len = answerObjs.length; j < len; j++) {
            answerObj = answerObjs[j];
            answerObjects.push(answerObj);
          }
          page++;
          return getAllProjectAnswers();
        }
      }, function(error) {
        return promise.reject(error);
      });
      return promise;
    };
    getAllProjectAnswers();
    return promise;
  };

  Parse.Cloud.define("listAllResponsesForPatient", function(request, response) {
    var endDate, patientIds, startDate;
    patientIds = request.params.patientIds;
    startDate = new Date(request.params.startDate);
    endDate = new Date(request.params.endDate);
    return listAllResponsesForPatient(patientIds, startDate, endDate).then(function(results) {
      console.log("___________");
      console.log("results.length " + results.length);
      console.log("___________");
      return response.success(results);
    }, function(error) {
      return response.error(error);
    });
  });

  listAllResponsesForPatient = function(patientIds, startDate, endDate) {
    var getAllPatientResponses, limit, page, promise, responseObjects;
    promise = new Parse.Promise();
    responseObjects = [];
    page = 0;
    limit = 100;
    getAllPatientResponses = function() {
      var responseQuery;
      responseQuery = new Parse.Query('Response');
      responseQuery.containedIn('patient', patientIds);
      responseQuery.descending('occurrenceDate');
      responseQuery.containedIn('status', ["completed", "missed", "base_line"]);
      responseQuery.greaterThanOrEqualTo('occurrenceDate', startDate);
      responseQuery.lessThanOrEqualTo('occurrenceDate', endDate);
      responseQuery.limit(limit);
      responseQuery.skip(page * limit);
      responseQuery.include('questionnaire');
      responseQuery.include('schedule');
      responseQuery.find().then(function(responseObjs) {
        var j, len, responseObj;
        console.log("___________");
        console.log("responseObjs.length " + responseObjs.length);
        console.log("___________");
        if (_.isEmpty(responseObjs)) {
          return promise.resolve(responseObjects);
        } else {
          for (j = 0, len = responseObjs.length; j < len; j++) {
            responseObj = responseObjs[j];
            responseObjects.push(responseObj);
          }
          page++;
          return getAllPatientResponses();
        }
      }, function(error) {
        return promise.reject(error);
      });
      return promise;
    };
    getAllPatientResponses();
    return promise;
  };

  Parse.Cloud.define("listAllAnswersForPatient", function(request, response) {
    var endDate, patientId, startDate;
    patientId = request.params.patientId;
    startDate = new Date(request.params.startDate);
    endDate = new Date(request.params.endDate);
    return listAllAnswersForPatient(patientId, startDate, endDate).then(function(results) {
      console.log("___________");
      console.log("results.length " + results.length);
      console.log("___________");
      return response.success(results);
    }, function(error) {
      return response.error(error);
    });
  });

  listAllAnswersForPatient = function(patientId, startDate, endDate) {
    var answerObjects, getAllPatientAnswers, limit, page, promise;
    promise = new Parse.Promise();
    answerObjects = [];
    page = 0;
    limit = 100;
    getAllPatientAnswers = function() {
      var answerQuery;
      answerQuery = new Parse.Query('Answer');
      answerQuery.equalTo('patient', patientId);
      answerQuery.descending('updatedAt');
      answerQuery.greaterThanOrEqualTo('occurrenceDate', startDate);
      answerQuery.lessThanOrEqualTo('occurrenceDate', endDate);
      answerQuery.limit(limit);
      answerQuery.skip(page * limit);
      answerQuery.include('question');
      answerQuery.include('response');
      answerQuery.include('option');
      answerQuery.find().then(function(answerObjs) {
        var answerObj, j, len;
        console.log("___________");
        console.log("answerObjs.length " + answerObjs.length);
        console.log("___________");
        if (_.isEmpty(answerObjs)) {
          return promise.resolve(answerObjects);
        } else {
          for (j = 0, len = answerObjs.length; j < len; j++) {
            answerObj = answerObjs[j];
            answerObjects.push(answerObj);
          }
          page++;
          return getAllPatientAnswers();
        }
      }, function(error) {
        return promise.reject(error);
      });
      return promise;
    };
    getAllPatientAnswers();
    return promise;
  };

  Parse.Cloud.define("getPatientsAnswers", function(request, response) {
    var endDate, patientIds, startDate;
    patientIds = request.params.patientIds;
    startDate = new Date(request.params.startDate);
    endDate = new Date(request.params.endDate);
    return getPatientsAnswers(patientIds, startDate, endDate).then(function(results) {
      console.log("___________");
      console.log("results.length " + results.length);
      console.log("___________");
      return response.success(results);
    }, function(error) {
      return response.error(error);
    });
  });

  getPatientsAnswers = function(patientIds, startDate, endDate) {
    var answerObjects, getAllPatientAnswers, limit, page, promise;
    promise = new Parse.Promise();
    answerObjects = [];
    page = 0;
    limit = 100;
    getAllPatientAnswers = function() {
      var answerQuery;
      answerQuery = new Parse.Query('Answer');
      answerQuery.containedIn('patient', patientIds);
      answerQuery.greaterThanOrEqualTo('occurrenceDate', startDate);
      answerQuery.lessThanOrEqualTo('occurrenceDate', endDate);
      answerQuery.descending('updatedAt');
      answerQuery.limit(limit);
      answerQuery.skip(page * limit);
      answerQuery.include('question');
      answerQuery.include('response');
      answerQuery.include('option');
      answerQuery.find().then(function(answerObjs) {
        var answerObj, j, len;
        console.log("___________");
        console.log("answerObjs.length " + answerObjs.length);
        console.log("___________");
        if (_.isEmpty(answerObjs)) {
          return promise.resolve(answerObjects);
        } else {
          for (j = 0, len = answerObjs.length; j < len; j++) {
            answerObj = answerObjs[j];
            answerObjects.push(answerObj);
          }
          page++;
          return getAllPatientAnswers();
        }
      }, function(error) {
        return promise.reject(error);
      });
      return promise;
    };
    getAllPatientAnswers();
    return promise;
  };

  _ = require('underscore.js');

  moment = require('cloud/moment.js');

  momenttimezone = require('cloud/moment-timezone.js');

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


  /*   
  Parse.Cloud.job 'createMissedResponse', (request, response) ->
     scheduleQuery = new Parse.Query('Schedule')
     scheduleQuery.exists("patient")
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
             if(parseInt(diffrence2) > 1)
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
  
          * save all responses
         Parse.Object.saveAll responseSaveArr
             .then (resObjs) ->
                  * update all schedule nextoccurrence
                 Parse.Object.saveAll scheduleSaveArr
                     .then (scheduleObjs) ->
                         response.success scheduleObjs
                     , (error) ->
                         response.error (error)   
             , (error) ->
                 response.error (error)
  
     , (error) ->
         response.error error
   */


  /*
  Parse.Cloud.define 'createMissedResponse', (request, response) ->
  
      scheduleQuery = new Parse.Query('Schedule')
      scheduleQuery.exists("patient")
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
              if(parseInt(diffrence2) > 1)
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
  
           * save all responses
          Parse.Object.saveAll responseSaveArr
              .then (resObjs) ->
                   * update all schedule nextoccurrence
                  Parse.Object.saveAll scheduleSaveArr
                      .then (scheduleObjs) ->
                          response.success scheduleObjs
                      , (error) ->
                          response.error (error)   
              , (error) ->
                  response.error (error)
  
      , (error) ->
          response.error error
   */

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
