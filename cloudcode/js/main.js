(function() {
  var _;

  _ = require('underscore.js');

  Parse.Cloud.define('doSetup', function(request, response) {
    var referenceCode, userObj;
    referenceCode = request.params.referenceCode;
    userObj = new Parse.Query(Parse.User);
    userObj.equalTo("referenceCode", referenceCode);
    return userObj.first().then(function(userObjects) {
      var result;
      if (!_.isEmpty(userObjects)) {
        result = {
          "message": 'reference code does not match',
          "code": '403'
        };
        return response.success(result);
      } else {
        result = {
          "id": userObjects.id,
          "name": userObjects.get('referenceNumber'),
          "code": userObjects.get('referenceCode')
        };
        return response.success(result);
      }
    }, function(error) {
      return response.error(error);
    });
  });

}).call(this);
