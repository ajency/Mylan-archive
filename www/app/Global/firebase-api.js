(function() {
  angular.module('PatientApp.Global').factory('FirebaseApi', [
    '$ionicPlatform', '$q', 'FirebaseKey', 'App', 'notificationService', 'Storage', 'PushConfig', function($ionicPlatform, $q, FirebaseKey, App, notificationService, Storage, PushConfig) {
      var User, firebaseCloudApi;
      User = {};
      Storage.vendorDetails('get').then(function(details) {
        return User = details;
      });
      firebaseCloudApi = {};
      firebaseCloudApi.setUser = function(data) {
        if (data == null) {
          data = null;
        }
        if (data) {
          return User = data;
        } else {
          return Storage.vendorDetails('get').then(function(details) {
            return User = details;
          });
        }
      };
      firebaseCloudApi.getDeviceToken = function() {
        var defer;
        defer = $q.defer();
        if (App.isWebView) {
          Storage.deviceToken('get').then(function(deviceToken) {
            var push;
            console.log(deviceToken, ' fetchFromLocalStorage');
            if (deviceToken) {
              return defer.resolve(deviceToken);
            } else {
              push = PushNotification.init(PushSettings);
              return push.on('registration', function(data) {
                Storage.deviceToken('set', data.registrationId);
                return defer.resolve(data.registrationId);
              });
            }
          });
        } else {
          Storage.deviceToken('set', 'dummyInstallationIdeee');
          defer.resolve('dummyInstallationIdeee');
        }
        return defer.promise;
      };
      firebaseCloudApi.firebaseInit = function() {
        console.log(' INITIALISING FIREBASE');
        return firebase.initializeApp(FirebaseKey);
      };
      firebaseCloudApi.getUser = function() {
        var defer;
        defer = $q.defer();
        firebase.database().ref('user/' + User.user_id).once('value').then(function(data) {
          return defer.resolve(data.val());
        }, function(error) {
          return defer.reject(error);
        });
        return defer.promise;
      };
      firebaseCloudApi.saveNotification = function() {
        var defer;
        console.log(User, ' USER');
        defer = $q.defer();
        firebase.database().ref('notification/36801').push({
          "createdAt": "2016-11-25T05:35:10.017Z",
          "data": {
            "availabilityStatus": {
              "first": "nodata",
              "second": "nodata"
            },
            "bdName": "Milan",
            "bdPhone": "+917840095788",
            "createdOn": "2016-11-25 11:05",
            "customerId": 32924,
            "customerName": "Velantan dsilva",
            "customerPhone": "+917845125412",
            "eventDate": "2016-11-29",
            "eventId": 10230,
            "event_type": "first_visit",
            "formatted_date": "Saturday, December 24 2016, 20:30 PM",
            "guestCount": 32,
            "meetingDetails": "",
            "partyAreaId": 894,
            "partyAreaName": "Bay Leaf 1",
            "partyAreaRent": null,
            "rmName": "Ajency Admin",
            "rmPhone": "+919049502624",
            "shortlistId": 126105,
            "startDate": "2016-12-24 20:30",
            "status": 2,
            "status_name": "Confirmed",
            "timeSlot": {
              "first": false,
              "second": false
            },
            "typeOfEvent": "Wedding",
            "updatedOn": "2016-11-25 11:05",
            "venueId": 445,
            "venueName": "Aura Grande",
            "venueSlug": "aura-grande-andheri-east"
          },
          "hasSaved": false,
          "hasSeen": false,
          "message": "Recce meeting scheduled on Saturday, December 24 2016, 20:30 PM for Velantan dsilva is Confirmed",
          "objectId": "03ikAu36Hr",
          "processed": true,
          "processedDate": {
            "__type": "Date",
            "iso": "2016-11-25T05:36:09.990Z"
          },
          "recipientUser": "afreenquadri@yahoo.com",
          "sentStatus": false,
          "title": "Recce meeting is Confirmed",
          "type": "edit_scheduled_event_status",
          "updatedAt": "2016-11-25T05:36:10.022Z"
        }).then(function(result) {
          return defer.resolve(result);
        }, function(error) {
          return defer.reject(error);
        });
        return defer.promise;
      };
      firebaseCloudApi.fetchNotifications = function(user_id) {
        var defer;
        if (user_id == null) {
          user_id = null;
        }
        if (!user_id) {
          user_id = User.user_id;
        }
        defer = $q.defer();
        firebase.database().ref('notification/' + user_id).once('value').then(function(data) {
          var i, j, keys, notificationList, notifications, ref, temp;
          if (data.val()) {
            notificationService.notificationList = [];
            temp = data.val();
            keys = Object.keys(temp);
            notifications = [];
            for (i = j = 0, ref = keys.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
              temp[keys[i]].id = keys[i];
              notifications.push(temp[keys[i]]);
            }
            notificationService.addNotificationtoList(notifications);
            notificationList = _.filter(notifications, function(value) {
              if (_.isObject(value)) {
                return value;
              }
            });
            return defer.resolve(notificationList);
          } else {
            return defer.resolve([]);
          }
        }, function(error) {
          return defer.reject(error);
        });
        return defer.promise;
      };
      firebaseCloudApi.fetchAllDevices = function(user_id) {
        var defer;
        if (user_id == null) {
          user_id = null;
        }
        if (!user_id) {
          user_id = User.user_id;
        }
        defer = $q.defer();
        firebase.database().ref('installation/' + user_id).once('value').then(function(data) {
          return defer.resolve(data.val());
        }, function(error) {
          return defer.reject(error);
        });
        return defer.promise;
      };
      firebaseCloudApi.updateNotificationStatus = function(notificationId) {
        var defer;
        console.log(User.user_id, notificationId);
        defer = $q.defer();
        firebase.database().ref('notification/' + User.user_id + '/' + notificationId).update({
          hasSeen: true
        }).then(function(result) {
          console.log(result, 'RESS');
          return defer.resolve(result);
        }, function(error) {
          return defer.reject(error);
        });
        return defer.promise;
      };
      firebaseCloudApi.registerDevice = function(user_id, authKey) {
        if (user_id == null) {
          user_id = null;
        }
        if (!user_id) {
          user_id = User.user_id;
        }
        return firebaseCloudApi.getDeviceToken().then(function(token) {
          console.log(token, 'DEVICETOKEN');
          return firebaseCloudApi.fetchAllDevices(user_id).then(function(result) {
            var flag, i, j, keys, ref;
            if (result) {
              keys = Object.keys(result);
              flag = false;
              for (i = j = 0, ref = keys.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
                if (result[keys[i]].deviceToken === token) {
                  flag = true;
                }
              }
              if (flag) {
                console.log('deviceAlreadyRegistered');
              } else {
                console.log('newDevice');
              }
              if (!flag) {
                return firebase.database().ref('installation/' + user_id).push({
                  authKey: authKey,
                  deviceToken: token
                });
              }
            } else {
              if (result === null) {
                return firebase.database().ref('installation/' + user_id).push({
                  authKey: authKey,
                  deviceToken: token
                });
              }
            }
          });
        });
      };
      firebaseCloudApi.logout = function() {
        return firebaseCloudApi.getDeviceToken().then(function(deviceToken) {
          if (!User) {
            Storage.vendorDetails('get').then(function(details) {
              return User = details;
            });
          }
          console.log('FIREBASE LOGOUT', User, deviceToken);
          return firebaseCloudApi.fetchAllDevices(User.user_id).then(function(result) {
            var flag, i, j, keys, ref, results;
            if (result) {
              keys = Object.keys(result);
              flag = false;
              results = [];
              for (i = j = 0, ref = keys.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
                if (result[keys[i]].deviceToken === deviceToken) {
                  console.log(keys[i]);
                  results.push(firebase.database().ref('installation/' + User.user_id + '/' + keys[i]).remove().then(function(result) {
                    return console.log(result);
                  }, function(error) {
                    return console.log(error);
                  }));
                } else {
                  results.push(void 0);
                }
              }
              return results;
            } else {
              return console.log('NO DEVICE FOUND');
            }
          }, function(error) {
            return console.log(error, 'error');
          });
        });
      };
      return firebaseCloudApi;
    }
  ]);

}).call(this);
