angular.module 'PatientApp.storage', []


.factory 'Storage', [()->

  Storage = {}

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

  Storage.refcode = (action,ref)->
    switch action
      when 'set'
        localforage.setItem 'refcode', ref

      when 'get'
        localforage.getItem 'refcode'      
  Storage
]