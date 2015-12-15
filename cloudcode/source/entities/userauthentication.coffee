Buffer = require('buffer').Buffer

# In the Data Browser, set the Class Permissions for these 2 classes to
# disallow public access for Get/Find/Create/Update/Delete operations.
# Only the master key should be able to query or write to these classes.

TokenRequest = Parse.Object.extend("TokenRequest")
TokenStorage = Parse.Object.extend("TokenStorage")

# Create a Parse ACL which prohibits public access.  This will be used
# in several places throughout the application, to explicitly protect
# Parse User, TokenRequest, and TokenStorage objects.

restrictedAcl = new Parse.ACL()
restrictedAcl.setPublicReadAccess(false)
restrictedAcl.setPublicWriteAccess(false)

Parse.Cloud.define 'loginParseUser', (request, response) ->
	authKey = request.params.authKey
	referenceCode = String(request.params.referenceCode)
	installationId = String(request.params.installationId)

	scheduleFlag = false

	querySchedule = new Parse.Query("Schedule")
	querySchedule.equalTo('patient', referenceCode)
	querySchedule.first()
	.then (scheduleObj) ->

		if(!_.isEmpty(scheduleObj))
			scheduleFlag = true


		queryTokenStorage = new Parse.Query("TokenStorage")
		queryTokenStorage.equalTo('referenceCode', referenceCode)
		queryTokenStorage.equalTo('installationId', installationId)

		queryTokenStorage.first(useMasterKey: true)
	    .then (tokenStorageObj) ->	

	    	if(_.isEmpty(tokenStorageObj))
	    		# create new user and store its reference in tokenstorage table
	    		appData =
	    			installationId: installationId
	    			referenceCode: referenceCode

	    		createNewUser(authKey, appData)
	    		.then (user)->
	    			result = 
	    				sessionToken: user.getSessionToken()
	    				scheduleFlag : scheduleFlag
	    			response.success result 
	    		, (error) ->
	    			response.error error
	    	else
	    		# check if authKey matches with the one stored 
	    		storedAuthKey = tokenStorageObj.get("authKey")
	    		user = tokenStorageObj.get("user")

	    		if storedAuthKey is authKey
	    			querySession = new Parse.Query(Parse.Session)
	    			querySession.equalTo('user', user)
	    			querySession.equalTo('installationId', installationId)
	    			querySession.first(useMasterKey: true)
	    			.then (sessionObj) ->
	    				sessionToken =  sessionObj.get('sessionToken')
		    			result = 
		    				sessionToken: sessionToken 
		    				scheduleFlag:scheduleFlag 
	    				response.success result 	    				 				
	    			, (error) ->
	    				response.error error 
	    		else
	    			# update new auth key
	    			tokenStorageObj.set "authKey" , authKey
	    			tokenStorageObj.save()
	    			.then (newTokenStorageObj) ->
		    			querySession = new Parse.Query(Parse.Session)
		    			querySession.equalTo('user', user)
		    			querySession.equalTo('installationId', installationId)
		    			querySession.first(useMasterKey: true)
		    			.then (sessionObj) ->
		    				sessionToken =  sessionObj.get('sessionToken')
			    			result = 
			    				sessionToken: sessionToken
			    				scheduleFlag : scheduleFlag  
		    				response.success result 	    				 				
		    			, (error) ->
		    				response.error error 
	    			, (error) ->
	    				response.error error

	, (error) ->
		response.error error	




createNewUser = (authKey, appData) ->
	promise = new Parse.Promise()
	user = new Parse.User

	# Generate a random username and password.
	username = new Buffer(24)
	password = new Buffer(24)
	
	_.times 24, (i) ->
		username.set i, _.random(0, 255)
		password.set i, _.random(0, 255)

	user.set 'username', username.toString('base64')
	user.set 'password', password.toString('base64')


	# Sign up the new User
	user.signUp()
	.then (user) ->

		# create a new TokenStorage object to store the user and laravel user association.		
		ts = new TokenStorage()
		ts.set 'authKey', authKey
		ts.set 'installationId', appData.installationId
		ts.set 'referenceCode', appData.referenceCode
		ts.set 'user', user
		ts.setACL restrictedAcl

		# Use the master key because TokenStorage objects should be protected.
		ts.save(null, useMasterKey: true)
		.then (TokenStorageObj) ->
			promise.resolve user
		, (error) ->
			promise.reject error

	, (error) ->
		promise.reject error

	promise
		    

	