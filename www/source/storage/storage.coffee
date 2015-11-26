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

    Storage.storageOperation = (options={}, cb={})->
        switch action
          when 'set'
            if _.isFunction cb then cb.call()
            else
                if _.isArray options then storeArray(options)
                else lsetItem options.name, options.value

          when 'get'
            if _.isFunction cb then cb.call()
            else 
                if _.isArray options.name then fetchItem options.name
                else fetchItem(options.name)

          when 'remove'
            if _.isFunction cb then cb.call()
            else removeItem(options)



    setItem = (name, value)->
        localforage.setItem name, value

    fetchItem = (name)->
        localforage.getItem name

    storeArray = (options)->
        angular.forEach options, (option)->
            setItem(option.name, option.value)

    fetchArray = (names)->
        data = []
        angular.forEach names, (name)->        
            data.push fetchItem name
        data

    removeItem = (options)->

        if _.isArray options.name
            angular.forEach options.name, (name)->        
                localforage.removeItem name
        else
            localforage.removeItem options.name





    Storage
]