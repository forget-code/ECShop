(function () {

	'use strict';

	angular
		.module('app')
		.config(config);

	config.$inject = ['$stateProvider', '$urlRouterProvider'];

	function config($stateProvider, $urlRouterProvider) {

		$stateProvider
			.state('activity-detail', {
				needAuth: false,
				url: '/activity-detail?activity',						
				templateUrl: 'modules/activity-detail/activity-detail.html'
			});


	}

})();
