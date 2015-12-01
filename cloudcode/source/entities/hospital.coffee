Parse.Cloud.define "addHospital", (request, response) ->
	hospitalObj = new Parse.Object("Hospital")
	hospitalObj.set 'name', request.params.hospitalName
	hospitalObj.set 'address', request.params.address
	hospitalObj.set 'primary_contact_number', request.params.primary_contact_number
	hospitalObj.set 'primary_email_address', request.params.primary_email_address
	hospitalObj.set 'website', request.params.website
	hospitalObj.set 'logo', request.params.logo
	hospitalObj.set 'contact_person_name', request.params.contact_person_name
	hospitalObj.set 'contact_person_email', request.params.contact_person_email
	hospitalObj.set 'contact_person_number', request.params.contact_person_number
	hospitalObj.save()
	.then (hospitalObj) ->
		response.success hospitalObj

	, (error) ->
		response.error error


Parse.Cloud.define "listHospital", (request, response) ->
	hospitalQuery = new Parse.Query "Hospital"
	hospitalQuery.find()
	.then (hospitalObjs) ->
		hospitalArray = []

		hospitalData = (hospitalObj) ->
			hospital = {}
			hospital['name']= hospitalObj.get('name')
			hospital['address']= hospitalObj.get('address')
			hospital['primary_contact_number']= hospitalObj.get('primary_contact_number')
			hospital['primary_email_address']= hospitalObj.get('primary_email_address')
			hospital['no_of_patients']= "to be added"
			hospital['no_of_users']= "to be added"
			hospital['no_of_doctors']= "to be added"
			hospital['no_of_flags']= "to be added"
			hospital['no_of_projects']= "to be added"
			hospitalArray.push(hospital)

		hospitalData hospitalObj for hospitalObj in hospitalObjs
		response.success hospitalArray

	, (error) ->
		response.error error

