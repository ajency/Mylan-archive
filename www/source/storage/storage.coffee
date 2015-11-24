angular.module 'PatientApp.storage', []


.factory 'Storage', [->


  Storage = {}

  ref = ''

  userInfo = {}


  Storage.setup = (action)->
    switch action
      when 'set'
        localforage.setItem 'app_setup_done', true

      when 'get'
        localforage.getItem 'app_setup_done'

  Storage.login = (action)->
    switch action
      when 'set'
        localforage.setItem 'logged', true

      when 'get'
        localforage.getItem 'logged'


  Storage.quizDetails = (action, params)->
    switch action
      when 'set'
        localforage.setItem 'quizDetail', params

      when 'get'
        localforage.getItem 'quizDetail'

      when 'remove'
        localforage.removeItem 'quizDetail'

  Storage.refcode = (action,refcode)->
    switch action
      when 'set'
        localforage.setItem 'refcode', refcode

      when 'get'
        localforage.getItem 'refcode'

  Storage.hospital_data = (action,hospital_data)->
    switch action
      when 'set'
        localforage.setItem 'hospital_details', hospital_data

      when 'get'
        localforage.getItem 'hospital_details'  

  Storage.user_data = (action,user_data)->
    switch action
      when 'set'
        localforage.setItem 'user_details', user_data

      when 'get'
        localforage.getItem 'user_details' 

  Storage.getNextQuestion = (action,questionNo)->
    switch action
      when 'set'
        localforage.setItem 'nextQuestion', questionNo

      when 'get'
        localforage.getItem 'nextQuestion' 

  Storage.setRefernce = (action,param)->
    switch action
      when 'set'
        ref = param

      when 'get'
        ref  

  Storage.setHospitalData = (action,data)->
    switch action
      when 'set'
          _.each data, (val, index)->
            userInfo[index] = val
      when 'get'
          userInfo

  Storage
]