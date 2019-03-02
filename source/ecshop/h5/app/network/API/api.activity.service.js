(function () {

    'use strict';

    angular
    .module('app')
    .factory('APIActivityService', APIActivityService);

    APIActivityService.$inject = ['$http', '$q', '$timeout', 'CacheFactory', 'ENUM'];

    function APIActivityService($http, $q, $timeout, CacheFactory, ENUM) {

        var service = new APIEndpoint( $http, $q, $timeout, CacheFactory, 'APIActivityService' );
        service.list = _list;
        service.get = _get;
        return service;

        function _list(params) {
            return this.fetch( '/v2/ecapi.activity.list', params, false, function(res){
                return ENUM.ERROR_CODE.OK == res.data.error_code ? res.data.activities : null;
            });
        }

        function _get(params) {
            return this.fetch( '/v2/ecapi.activity.get', params, false, function(res){
                return ENUM.ERROR_CODE.OK == res.data.error_code ? res.data.activity : null;
            });
        }
    }

})();
