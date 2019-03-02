(function () {

	'use strict';

	angular
		.module('app')
		.factory('ActivityModel', ActivityModel);

	ActivityModel.$inject = ['$http', '$q', '$timeout', '$rootScope', 'CacheFactory', 'AppAuthenticationService', 'API', 'ENUM'];

	function ActivityModel($http, $q, $timeout, $rootScope, CacheFactory, AppAuthenticationService, API, ENUM) {

		var PER_PAGE = 10;

		var service = {};
		service.isEmpty = false;
		service.isLoaded = false;
		service.isLoading = false;
		service.isLastPage = false;
		service.status = null;
		service.activities = null;
		service.fetch = _fetch;
		service.reload = _reload;
		service.loadMore = _loadMore;
		service.page = 0;
		return service;

		function _reload() {

			if (this.isLoading)
				return;
			this.isEmpty = false;
			this.isLoaded = false;
			this.isLastPage = false;

			this.fetch(1, PER_PAGE);
		}

		function _loadMore() {


			if (this.isLoading)
				return;
			if (this.isLastPage)
				return;

			if (this.activities && this.activities.length) {
				this.fetch((this.activities.length / PER_PAGE) + 1, PER_PAGE);
			} else {
				this.fetch(1, PER_PAGE);
			}
		}

		function _fetch(page, perPage) {


			this.isLoading = true;

			var params = {
				page: page,
				per_page: perPage
			};

			var _this = this;
			_this.page = page;
			API.activity.list(params).then(function (activities) {
				if(_this.page <= 1){
					_this.activities = activities;					
				}
				else{
					_this.activities = _this.activities ? _this.activities.concat(activities) : activities;	
				}
				
				_this.isEmpty = (_this.activities && _this.activities.length) ? false : true;
				_this.isLoaded = true;
				_this.isLoading = false;
				_this.isLastPage = (activities && activities.length < perPage) ? !_this.isEmpty : false;
			});
		}


	}

})();